<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganizationSettingsResource\Pages\EditOrganizationSettings;
use App\Models\OrganizationSettings;
use App\Models\User;
use App\Services\MediaLibrary;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FaviconSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_uploads_replaces_and_removes_a_favicon_on_public_and_admin_pages(): void
    {
        Storage::fake('public');
        $organization = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->fillForm(['favicon_path' => UploadedFile::fake()->image('favicon.png', 512, 512)])
            ->call('save')->assertHasNoFormErrors();

        $firstPath = $organization->fresh()->favicon_path;
        $this->assertStringStartsWith('seo/favicons/', $firstPath);
        Storage::disk('public')->assertExists($firstPath);
        $firstUrl = Storage::disk('public')->url($firstPath);
        $this->get('/')->assertOk()->assertSee('<link rel="icon" href="'.$firstUrl.'"', false);
        $this->get('/privacy')->assertOk()->assertSee('<link rel="icon" href="'.$firstUrl.'"', false);
        $this->get('/admin')->assertOk()->assertSee($firstUrl, false);
        $image = app(MediaLibrary::class)->images()->firstWhere('path', $firstPath);
        $this->assertNotNull($image);
        $this->assertContains('Organization ? Favicon', array_column($image['uses'], 'label'));

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->fillForm(['favicon_path' => []])
            ->fillForm(['favicon_path' => UploadedFile::fake()->image('replacement.png', 64, 64)])
            ->call('save')->assertHasNoFormErrors();

        $replacementPath = $organization->fresh()->favicon_path;
        $this->assertNotSame($firstPath, $replacementPath);
        Storage::disk('public')->assertExists($replacementPath);
        $this->get('/')->assertOk()->assertSee(Storage::disk('public')->url($replacementPath), false)->assertDontSee($firstUrl, false);

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->fillForm(['favicon_path' => []])->call('save')->assertHasNoFormErrors();

        $this->assertNull($organization->fresh()->favicon_path);
        $this->get('/')->assertOk()->assertSee('<link rel="icon" href="'.asset('images/logo-icon.png').'"', false);
    }

    public function test_genuine_ico_uploads_are_accepted(): void
    {
        Storage::fake('public');
        $organization = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());
        $embeddedImage = UploadedFile::fake()->image('embedded.png', 32, 32);
        $png = file_get_contents($embeddedImage->getPathname());
        $ico = pack('vvv', 0, 1, 1).pack('CCCCvvVV', 32, 32, 0, 0, 1, 32, strlen($png), 22).$png;

        $iconFile = UploadedFile::fake()->createWithContent('favicon.ico', $ico);
        $upload = $iconFile->mimeType((new \finfo(FILEINFO_MIME_TYPE))->file($iconFile->getPathname()));

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->fillForm(['favicon_path' => $upload])
            ->call('save')->assertHasNoFormErrors();

        $path = $organization->fresh()->favicon_path;
        $this->assertStringEndsWith('.ico', $path);
        Storage::disk('public')->assertExists($path);
        $this->get('/')->assertOk()->assertSee(Storage::disk('public')->url($path), false);
    }

    public function test_unsupported_and_oversized_uploads_are_rejected_without_changing_saved_icon(): void
    {
        Storage::fake('public');
        $organization = OrganizationSettings::current();
        $organization->update(['favicon_path' => 'seo/favicons/saved.png']);
        $this->actingAs(User::factory()->create());

        foreach ([
            UploadedFile::fake()->createWithContent('unsafe.php', '<?php echo "unsafe";'),
            UploadedFile::fake()->createWithContent('unsafe.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            UploadedFile::fake()->image('large.png', 64, 64)->size(1025),
        ] as $upload) {
            Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
                ->fillForm(['favicon_path' => []])->fillForm(['favicon_path' => $upload])
                ->call('save')->assertHasFormErrors(['favicon_path']);
            $this->assertSame('seo/favicons/saved.png', $organization->fresh()->favicon_path);
        }
    }

    public function test_missing_or_invalid_saved_icons_fall_back_and_guest_cannot_edit_settings(): void
    {
        Storage::fake('public');
        $organization = OrganizationSettings::current();

        foreach ([null, 'seo/favicons/missing.png', 'https://example.test/icon.png', 'seo/favicons/../../private.png'] as $path) {
            $organization->update(['favicon_path' => $path]);
            $this->get('/')->assertOk()->assertSee('<link rel="icon" href="'.asset('images/logo-icon.png').'"', false);
        }

        $this->get('/admin/login')->assertOk()->assertSee(asset('images/logo-icon.png'), false);
        $this->get('/admin/organization-settings/'.$organization->id.'/edit')->assertRedirect('/admin/login');
    }
}

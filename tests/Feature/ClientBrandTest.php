<?php

namespace Tests\Feature;

use App\Filament\Resources\ClientBrandResource\Pages\CreateClientBrand;
use App\Filament\Resources\ClientBrandResource\Pages\EditClientBrand;
use App\Models\ClientBrand;
use App\Models\User;
use App\Services\MediaLibrary;
use Database\Seeders\ClientBrandSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ClientBrandTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_add_replace_and_remove_a_brand_logo(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateClientBrand::class)->fillForm([
            'name' => 'Example Brand',
            'logo_path' => UploadedFile::fake()->image('brand.png', 300, 120),
            'is_published' => true,
            'sort_order' => 5,
        ])->call('create')->assertHasNoFormErrors();

        $brand = ClientBrand::where('name', 'Example Brand')->firstOrFail();
        $firstPath = $brand->logo_path;
        Storage::disk('public')->assertExists($firstPath);
        $this->get('/')->assertOk()->assertSee('src="'.$brand->logoUrl().'"', false)->assertSee('alt="Example Brand"', false);
        $this->assertContains('Client brand: Example Brand / Logo', array_column(app(MediaLibrary::class)->images()->firstWhere('path', $firstPath)['uses'], 'label'));

        Livewire::test(EditClientBrand::class, ['record' => $brand->id])
            ->fillForm(['logo_path' => []])
            ->fillForm(['logo_path' => UploadedFile::fake()->image('replacement.webp', 300, 120), 'name' => 'Renamed Brand'])
            ->call('save')->assertHasNoFormErrors();

        $this->assertNotSame($firstPath, $brand->fresh()->logo_path);
        $this->assertSame('Renamed Brand', $brand->fresh()->name);
        Storage::disk('public')->assertExists($brand->fresh()->logo_path);

        Livewire::test(EditClientBrand::class, ['record' => $brand->id])
            ->fillForm(['logo_path' => []])->call('save')->assertHasNoFormErrors();

        $this->assertNull($brand->fresh()->logo_path);
        $this->get('/')->assertOk()->assertSee('Renamed Brand')->assertDontSee('class="client-brand-image"', false);
    }

    public function test_publishing_ordering_and_deleting_control_the_public_brand_strip(): void
    {
        $later = ClientBrand::factory()->create(['name' => 'Later Brand', 'sort_order' => 20]);
        ClientBrand::factory()->create(['name' => 'First Brand', 'sort_order' => 1]);
        ClientBrand::factory()->unpublished()->create(['name' => 'Hidden Brand']);
        $this->get('/')->assertOk()->assertSeeInOrder(['First Brand', 'Later Brand'])->assertDontSee('Hidden Brand');

        $this->actingAs(User::factory()->create());
        Livewire::test(EditClientBrand::class, ['record' => $later->id])
            ->fillForm(['is_published' => false])->call('save')->assertHasNoFormErrors();
        $this->assertFalse($later->fresh()->is_published);
        $this->get('/')->assertOk()->assertDontSee('Later Brand');

        Livewire::test(EditClientBrand::class, ['record' => $later->id])->callAction('delete');
        $this->assertModelMissing($later);
    }

    public function test_invalid_uploads_and_missing_names_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        foreach ([
            UploadedFile::fake()->createWithContent('unsafe.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
            UploadedFile::fake()->image('large.png')->size(2049),
        ] as $upload) {
            Livewire::test(CreateClientBrand::class)->fillForm(['name' => 'Rejected Brand', 'logo_path' => $upload])
                ->call('create')->assertHasFormErrors(['logo_path']);
        }
        Livewire::test(CreateClientBrand::class)->fillForm(['name' => ''])->call('create')->assertHasFormErrors(['name']);
        $this->assertDatabaseEmpty('client_brands');
    }

    public function test_missing_or_invalid_logos_fall_back_to_escaped_names_and_guests_cannot_edit(): void
    {
        Storage::fake('public');
        $brand = ClientBrand::factory()->create(['name' => '<script>Brand</script>']);

        foreach (['brands/missing.png', 'https://example.test/logo.png', 'brands/../private.png', 'brands/unsafe.svg'] as $path) {
            $brand->update(['logo_path' => $path]);
            $this->assertNull($brand->logoUrl());
            $this->get('/')->assertOk()->assertSee('&lt;script&gt;Brand&lt;/script&gt;', false)
                ->assertDontSee('class="client-brand-image"', false);
        }
        $this->get('/admin/client-brands')->assertRedirect('/admin/login');
        $this->get('/admin/client-brands/'.$brand->id.'/edit')->assertRedirect('/admin/login');
    }

    public function test_seed_preserves_existing_logos_visibility_and_order(): void
    {
        $brand = ClientBrand::factory()->create(['name' => 'Ask Media', 'logo_path' => 'brands/custom.png', 'is_published' => false, 'sort_order' => 99]);
        $this->seed(ClientBrandSeeder::class);
        $this->seed(ClientBrandSeeder::class);
        $this->assertDatabaseCount('client_brands', 10);
        $this->assertSame('brands/custom.png', $brand->fresh()->logo_path);
        $this->assertFalse($brand->fresh()->is_published);
        $this->assertSame(99, $brand->fresh()->sort_order);
    }

    public function test_empty_brand_collection_hides_the_section(): void
    {
        $this->get('/')->assertOk()->assertDontSee('class="clients-section"', false);
    }
}

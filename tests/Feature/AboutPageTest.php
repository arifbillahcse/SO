<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_about_page_uses_first_team_record_and_resolves_uploaded_founder_photo(): void
    {
        TeamMember::create(['name' => 'Second team member', 'role' => 'Engineer', 'bio' => 'Team biography.', 'sort_order' => 10]);
        TeamMember::create([
            'name' => 'Primary founder', 'role' => 'Founder', 'bio' => 'Founder biography.', 'short_role' => 'Founder and engineer',
            'photo_path' => 'team/founder.png', 'sort_order' => 1,
        ]);

        $this->get('/about-us')->assertOk()->assertSee('Primary founder')->assertSee('Founder and engineer')
            ->assertSee(Storage::disk('public')->url('team/founder.png'), false)
            ->assertDontSee('Second team member');
    }

    public function test_about_page_has_safe_founder_fallback_without_unverified_legacy_claims(): void
    {
        $this->get('/about-us')->assertOk()->assertSee('Md. Arif Billah')
            ->assertSee(asset('images/arif.png'), false)->assertSee('In development')
            ->assertDontSee('WordPress VIP Gold Partner')->assertDontSee('13,768')
            ->assertDontSee('Fortune 500')->assertDontSee('Tropicana');
    }

    public function test_existing_public_asset_paths_for_founder_photos_are_preserved(): void
    {
        TeamMember::create(['name' => 'Site founder', 'role' => 'Founder', 'bio' => 'Founder biography.', 'photo_path' => 'images/arif.png']);
        $this->get('/about-us')->assertOk()->assertSee(asset('images/arif.png'), false)
            ->assertDontSee('/storage/images/arif.png', false);
    }
}

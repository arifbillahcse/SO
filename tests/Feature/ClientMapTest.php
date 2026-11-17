<?php

namespace Tests\Feature;

use App\Filament\Resources\OrganizationSettingsResource\Pages\EditOrganizationSettings;
use App\Models\OrganizationSettings;
use App\Models\User;
use Database\Seeders\ClientCountriesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientMapTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_map_counts_unique_known_locations_and_ignores_invalid_saved_values(): void
    {
        OrganizationSettings::current()->update(['client_countries' => ['US', 'BD', 'US', 'XX', '<script>', ['CA']]]);

        $this->get('/about-us')->assertOk()->assertSee('Explore all 2 countries &amp; territories', false)
            ->assertSee('data-map-country="US"', false)->assertSee('data-map-country="BD"', false)
            ->assertDontSee('data-map-country="XX"', false)->assertDontSee('data-map-country="CA"', false)
            ->assertSee('#client-map [data-country="US"]{fill:var(--map-accent)', false)
            ->assertDontSee('#client-map [data-country="CA"]{fill:var(--map-accent)', false);
    }

    public function test_admin_can_change_locations_and_hide_map_without_losing_list(): void
    {
        $organization = OrganizationSettings::current();
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->fillForm(['client_countries' => ['CA', 'GB'], 'client_map_enabled' => true])
            ->call('save')->assertHasNoFormErrors();

        $this->assertSame(['CA', 'GB'], $organization->fresh()->client_countries);
        $this->get('/about-us')->assertOk()->assertSee('data-map-country="CA"', false)
            ->assertSee('Explore all 2 countries &amp; territories', false);

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->fillForm(['client_map_enabled' => false])->call('save')->assertHasNoFormErrors();

        $this->assertFalse($organization->fresh()->client_map_enabled);
        $this->assertSame(['CA', 'GB'], $organization->fresh()->client_countries);
        $this->get('/about-us')->assertOk()->assertDontSee('id="client-map"', false);
    }

    public function test_homepage_map_uses_the_shared_country_list_and_visibility_setting(): void
    {
        $organization = OrganizationSettings::current();
        $organization->update(['client_countries' => ['CA', 'GB'], 'client_map_enabled' => true]);

        foreach (['/', '/about-us'] as $page) {
            $this->get($page)->assertOk()->assertSee('id="client-map"', false)
                ->assertSee('Explore all 2 countries &amp; territories', false)
                ->assertSee('data-map-country="CA"', false)->assertSee('data-map-country="GB"', false)
                ->assertDontSee('data-map-country="US"', false);
        }

        $organization->update(['client_map_enabled' => false]);
        $this->get('/')->assertOk()->assertDontSee('id="client-map"', false);

        $organization->update(['client_map_enabled' => true, 'client_countries' => []]);
        $this->get('/')->assertOk()->assertDontSee('id="client-map"', false);
    }

    public function test_invalid_location_is_rejected_and_guest_cannot_edit(): void
    {
        $organization = OrganizationSettings::current();
        $organization->update(['client_countries' => ['GB']]);
        $this->get('/admin/organization-settings/'.$organization->id.'/edit')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create());

        Livewire::test(EditOrganizationSettings::class, ['record' => $organization->id])
            ->set('data.client_countries', ['XX'])->call('save')->assertHasFormErrors(['client_countries.0']);

        $this->assertSame(['GB'], $organization->fresh()->client_countries);
    }

    public function test_no_map_or_invented_count_is_shown_without_locations(): void
    {
        OrganizationSettings::current()->update(['client_countries' => []]);
        $this->get('/about-us')->assertOk()->assertDontSee('id="client-map"', false);
    }

    public function test_import_seeds_verified_locations_once_and_preserves_admin_edits(): void
    {
        $this->seed(ClientCountriesSeeder::class);
        $organization = OrganizationSettings::current();
        $this->assertCount(53, $organization->clientCountries());
        $this->assertArrayHasKey('AU', $organization->clientCountries());
        $this->assertArrayHasKey('BS', $organization->clientCountries());
        $this->assertArrayHasKey('SA', $organization->clientCountries());

        $organization->update(['client_countries' => ['GB']]);
        $this->seed(ClientCountriesSeeder::class);
        $this->assertSame(['GB'], $organization->fresh()->client_countries);

        $organization->update(['client_countries' => []]);
        $this->seed(ClientCountriesSeeder::class);
        $this->assertSame([], $organization->fresh()->client_countries);
    }
}

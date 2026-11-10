<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeded inside the test transaction so rows don't leak into other test classes.
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/page-seos')->assertRedirect('/admin/login');
    }

    public function test_every_admin_list_create_and_edit_screen_renders(): void
    {
        $this->actingAs(User::factory()->create());

        $checked = 0;
        foreach (Route::getRoutes() as $route) {
            $name = (string) $route->getName();
            if (! str_starts_with($name, 'filament.admin.resources.') || ! in_array('GET', $route->methods(), true)) {
                continue;
            }

            if (str_contains($route->uri(), '{record}')) {
                $resource = collect(filament()->getPanel('admin')->getResources())
                    ->first(fn ($r) => str_starts_with($name, 'filament.admin.resources.'.$r::getSlug().'.'));
                /** @var Model|null $record */
                $record = $resource ? $resource::getModel()::query()->first() : null;
                if (! $record) {
                    continue;
                }
                $url = str_replace('{record}', $record->getRouteKey(), '/'.$route->uri());
            } else {
                $url = '/'.$route->uri();
            }

            $this->get($url)->assertOk();
            $checked++;
        }

        $this->assertGreaterThan(25, $checked);
    }
}

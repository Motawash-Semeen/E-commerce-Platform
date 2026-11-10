<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_default_to_an_active_shop_owner(): void
    {
        $user = User::factory()->create()->fresh();

        $this->assertTrue($user->isShopOwner());
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->is_active);
    }

    public function test_role_cannot_be_mass_assigned(): void
    {
        $user = new User(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secret', 'role' => 'admin']);

        $this->assertSame(User::ROLE_SHOP_OWNER, $user->role);
    }

    public function test_guests_are_sent_to_login_from_both_areas(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/owner/dashboard')->assertRedirect(route('login'));
    }

    public function test_admin_can_use_the_admin_area_but_not_the_owner_area(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/dashboard')->assertOk()->assertSee('Admin dashboard');
        $this->get('/admin/products')->assertOk();
        $this->get('/owner/dashboard')->assertForbidden();
    }

    public function test_shop_owner_can_use_the_owner_area_but_not_the_admin_area(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/owner/dashboard')->assertOk()->assertSee('Shop owner dashboard');

        foreach (['/admin/dashboard', '/admin/categories', '/admin/subcategories', '/admin/products', '/admin/log-viewer'] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_login_sends_each_role_to_its_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        auth()->logout();

        $owner = User::factory()->create();
        $this->post('/login', ['email' => $owner->email, 'password' => 'password'])
            ->assertRedirect(route('owner.dashboard'));
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create();

        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Your account is disabled.']);

        $this->assertGuest();
    }

    public function test_navbar_shows_links_by_role(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/dashboard')
            ->assertSee(route('admin.categories.index'), false)
            ->assertSee(route('log-viewer.index'), false);

        $this->actingAs(User::factory()->create())
            ->get('/owner/dashboard')
            ->assertSee('My dashboard')
            ->assertDontSee(route('admin.categories.index'), false)
            ->assertDontSee(route('log-viewer.index'), false);
    }
}

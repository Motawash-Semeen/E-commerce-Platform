<?php

namespace Tests\Feature\Admin;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShopOwnerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->actingAs($this->admin);
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Rina Akter',
            'email' => 'rina@example.com',
            'password' => 'secret-pass-1',
            'password_confirmation' => 'secret-pass-1',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_only_admins_can_reach_the_shop_owner_pages(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.shop-owners.index'))
            ->assertForbidden();
    }

    public function test_index_lists_owners_with_their_shop_and_status_but_not_admins(): void
    {
        // The list is sorted by name, 10 per page: the owners checked below get names
        // that sort first, and the 10 extra owners sort last, so page 1 is predictable.
        $withShop = User::factory()->create(['name' => 'Aaron With Shop']);
        Shop::factory()->for($withShop, 'owner')->create(['name' => 'Rina Fashion House']);
        User::factory()->inactive()->create(['name' => 'Karim Without Shop']);
        User::factory()->count(10)->has(Shop::factory())
            ->sequence(fn ($sequence) => ['name' => 'Zz Owner '.str_pad($sequence->index, 2, '0', STR_PAD_LEFT)])
            ->create();   // > 1 page in total

        $this->get(route('admin.shop-owners.index'))
            ->assertOk()
            ->assertSee('Aaron With Shop')
            ->assertSee('Rina Fashion House')
            ->assertSee('Karim Without Shop')
            ->assertSee('No shop')
            ->assertSee('Disabled')
            ->assertDontSee($this->admin->email)
            ->assertSee('page-link', false);
    }

    public function test_create_hashes_the_password_and_forces_the_shop_owner_role(): void
    {
        $this->post(route('admin.shop-owners.store'), $this->validData(['role' => 'admin']))
            ->assertRedirect(route('admin.shop-owners.index'))
            ->assertSessionHas('success');

        $owner = User::where('email', 'rina@example.com')->sole();

        $this->assertTrue($owner->isShopOwner());
        $this->assertTrue($owner->is_active);
        $this->assertNotSame('secret-pass-1', $owner->password);
        $this->assertTrue(Hash::check('secret-pass-1', $owner->password));
    }

    public function test_create_can_make_a_disabled_account(): void
    {
        $this->post(route('admin.shop-owners.store'), $this->validData(['is_active' => '0']));

        $this->assertFalse(User::where('email', 'rina@example.com')->sole()->is_active);
    }

    public function test_create_validates_email_and_password(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post(route('admin.shop-owners.store'), $this->validData(['email' => 'taken@example.com']))
            ->assertSessionHasErrors('email');
        $this->post(route('admin.shop-owners.store'), $this->validData(['password' => 'short', 'password_confirmation' => 'short']))
            ->assertSessionHasErrors('password');
        $this->post(route('admin.shop-owners.store'), $this->validData(['password_confirmation' => 'different-1']))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['name' => 'Rina Akter']);
    }

    public function test_update_keeps_the_password_when_left_blank(): void
    {
        $owner = User::factory()->create(['email' => 'old@example.com']);
        $oldHash = $owner->password;

        $this->put(route('admin.shop-owners.update', $owner), [
            'name' => 'New Name',
            'email' => 'old@example.com',    // own email: unique check ignores self
            'password' => '',
            'password_confirmation' => '',
            'is_active' => '0',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.shop-owners.index'));

        $owner->refresh();
        $this->assertSame('New Name', $owner->name);
        $this->assertSame($oldHash, $owner->password);
        $this->assertFalse($owner->is_active);
        $this->assertTrue($owner->isShopOwner());
    }

    public function test_update_can_set_a_new_password_and_rejects_another_users_email(): void
    {
        $owner = User::factory()->create();
        User::factory()->create(['email' => 'someone@example.com']);

        $this->put(route('admin.shop-owners.update', $owner), $this->validData(['email' => 'someone@example.com']))
            ->assertSessionHasErrors('email');

        $this->put(route('admin.shop-owners.update', $owner), $this->validData([
            'email' => $owner->email,
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ]))->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('brand-new-pass', $owner->refresh()->password));
    }

    public function test_admin_accounts_cannot_be_edited_or_deleted_here(): void
    {
        $otherAdmin = User::factory()->admin()->create();

        $this->get(route('admin.shop-owners.edit', $otherAdmin))->assertNotFound();
        $this->put(route('admin.shop-owners.update', $otherAdmin), $this->validData())->assertNotFound();
        $this->delete(route('admin.shop-owners.destroy', $otherAdmin))->assertNotFound();
        $this->delete(route('admin.shop-owners.destroy', $this->admin))->assertNotFound();

        $this->assertModelExists($otherAdmin);
        $this->assertTrue($otherAdmin->refresh()->isAdmin());
    }

    public function test_an_owner_with_a_shop_cannot_be_deleted(): void
    {
        $shop = Shop::factory()->create(['name' => 'Corner Store']);

        $this->from(route('admin.shop-owners.index'))
            ->delete(route('admin.shop-owners.destroy', $shop->owner))
            ->assertRedirect(route('admin.shop-owners.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($shop->owner);
    }

    public function test_an_owner_without_a_shop_can_be_deleted(): void
    {
        $owner = User::factory()->create();

        $this->delete(route('admin.shop-owners.destroy', $owner))
            ->assertRedirect(route('admin.shop-owners.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($owner);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_products(): void
    {
        $this->get(route('admin.products.index'))
            ->assertRedirect(route('login'));
    }
}

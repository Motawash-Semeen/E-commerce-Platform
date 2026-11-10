<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogViewerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_the_log_viewer_or_its_api(): void
    {
        $this->get('/admin/log-viewer')->assertRedirect(route('login'));
        $this->getJson('/admin/log-viewer/api/files')->assertUnauthorized();
    }

    public function test_admin_can_open_the_log_viewer(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/log-viewer')->assertOk();
        $this->getJson('/admin/log-viewer/api/files')->assertOk();
    }
}

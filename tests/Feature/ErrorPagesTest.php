<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_page_shows_the_custom_404(): void
    {
        $this->get('/products/this-product-does-not-exist')
            ->assertNotFound()
            ->assertSee('Page not found')
            ->assertSee('Back to shop');
    }

    public function test_server_error_shows_the_custom_500_without_details(): void
    {
        config(['app.debug' => false]);
        Route::get('/_test/boom', fn () => throw new RuntimeException('secret internal detail'));

        $this->get('/_test/boom')
            ->assertStatus(500)
            ->assertSee('Something went wrong')
            ->assertDontSee('secret internal detail');
    }

    public function test_every_custom_error_page_renders(): void
    {
        $pages = [
            403 => 'Access denied',
            419 => 'Page expired',
            429 => 'Too many requests',
            503 => 'Back soon',
        ];

        foreach ($pages as $code => $title) {
            Route::get("/_test/{$code}", fn () => abort($code));

            $this->get("/_test/{$code}")
                ->assertStatus($code)
                ->assertSee($title);
        }
    }
}

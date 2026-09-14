<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_navbar_contains_light_dark_theme_toggles(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('name="color-scheme" content="light dark"', false);
        $response->assertSee('id="mobileThemeToggleBtn"', false);
        $response->assertSee('id="desktopThemeToggleBtn"', false);
        $response->assertSee('theme-toggle-btn', false);
        $response->assertSee('nham_theme', false);
    }

    public function test_admin_sidebar_and_topbar_contain_theme_toggles(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Manager',
            'email' => 'admin_theme@example.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('name="color-scheme" content="light dark"', false);
        $response->assertSee('id="sidebarThemeToggle"', false);
        $response->assertSee('id="topbarThemeToggle"', false);
        $response->assertSee('admin-theme-toggle-btn', false);
        $response->assertSee('nham_theme', false);
    }

    public function test_auth_pages_contain_theme_toggle_and_color_scheme(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('name="color-scheme" content="light dark"', false);
        $response->assertSee('theme-toggle-btn', false);
        $response->assertSee('nham_theme', false);
    }
}

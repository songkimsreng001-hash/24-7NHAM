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

    public function test_sweetalert_font_colors_and_theme_adapt_to_light_and_dark_mode(): void
    {
        // 1. Frontend layout contains SweetAlert light and dark rules
        $frontResp = $this->get('/');
        $frontResp->assertOk();
        $frontResp->assertSee('.swal2-title', false);
        $frontResp->assertSee('html[data-bs-theme="dark"] .swal2-title', false);
        $frontResp->assertSee('html[data-bs-theme="dark"] .swal2-html-container', false);
        $frontResp->assertSee('html[data-bs-theme="dark"] .swal2-popup', false);
        $frontResp->assertSee('color: #f8fafc', false);
        $frontResp->assertSee('color: #111827', false);

        // 2. Admin dashboard contains SweetAlert light and dark rules
        $admin = User::factory()->create(['role' => 'admin']);
        $adminResp = $this->actingAs($admin)->get(route('dashboard'));
        $adminResp->assertOk();
        $adminResp->assertSee('.swal2-title', false);
        $adminResp->assertSee('html[data-bs-theme="dark"] .swal2-title', false);
        $adminResp->assertSee('html[data-bs-theme="dark"] .swal2-html-container', false);

        // 3. Auth pages contain SweetAlert light and dark rules
        $authResp = $this->get(route('login'));
        $authResp->assertOk();
        $authResp->assertSee('.swal2-title', false);
        $authResp->assertSee('html[data-bs-theme="dark"] .swal2-title', false);
        $authResp->assertSee('html[data-bs-theme="dark"] .swal2-html-container', false);
    }
}

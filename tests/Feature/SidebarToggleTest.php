<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sidebar_toggle_elements_and_markup(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Tester',
            'email' => 'admin_sidebar@example.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();

        // Check toggle button
        $response->assertSee('id="sidebar-toggle"', false);
        $response->assertSee('sidebar-toggle-btn', false);
        $response->assertSee('title="Toggle Sidebar"', false);

        // Check sidebar navigation items have both icons and nav-text
        $response->assertSee('class="nav-text">Dashboard</span>', false);
        $response->assertSee('title="Dashboard"', false);

        $response->assertSee('class="nav-text">Products</span>', false);
        $response->assertSee('title="Products"', false);

        $response->assertSee('class="nav-text">Categories</span>', false);
        $response->assertSee('title="Categories"', false);

        $response->assertSee('class="nav-text">All Orders</span>', false);
        $response->assertSee('title="All Orders"', false);

        $response->assertSee('class="nav-text">Profile</span>', false);
        $response->assertSee('title="Profile"', false);

        $response->assertSee('class="nav-text">Help & Support</span>', false);
        $response->assertSee('title="Help & Support"', false);

        $response->assertSee('class="nav-text">Log Out</span>', false);
        $response->assertSee('title="Log Out"', false);

        // Check user section title
        $response->assertSee('class="sidebar-user" title="Admin Tester"', false);
    }

    public function test_admin_sidebar_collapsed_css_rules_and_transitions(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Tester',
            'email' => 'admin_sidebar_css@example.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();

        // Check collapsed layout variables and rules
        $response->assertSee('--sidebar-collapsed-w:72px;', false);
        $response->assertSee('html.sidebar-collapsed #sidebar', false);
        $response->assertSee('html.sidebar-collapsed #topbar', false);
        $response->assertSee('html.sidebar-collapsed #main-wrapper', false);

        // Check that text elements are hidden in collapsed state
        $response->assertSee('html.sidebar-collapsed .nav-text', false);
        $response->assertSee('html.sidebar-collapsed .brand-name', false);
        $response->assertSee('html.sidebar-collapsed .sidebar-search', false);
        $response->assertSee('html.sidebar-collapsed .sidebar-promo', false);
        $response->assertSee('html.sidebar-collapsed .sidebar-user-main', false);

        // Check smooth transitions
        $response->assertSee('transition:margin-left .25s', false);
        $response->assertSee('transition:width .25s', false);
    }

    public function test_admin_sidebar_toggle_javascript_logic(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Tester',
            'email' => 'admin_sidebar_js@example.com',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();

        // Check localStorage check in head script to prevent flash
        $response->assertSee("localStorage.getItem('nham_sidebar_collapsed')", false);

        // Check toggle handler functions in main script
        $response->assertSee('setSidebarCollapsed', false);
        $response->assertSee('isSidebarCollapsed', false);
        $response->assertSee("localStorage.setItem('nham_sidebar_collapsed'", false);
    }
}

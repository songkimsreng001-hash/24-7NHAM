<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAssignRoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Main Admin',
            'email' => 'admin_role_test@example.com',
            'role' => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'name' => 'Jane Customer',
            'email' => 'jane_customer@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_admin_dashboard_displays_user_roles_management_section(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('User Roles & Access Management', false);
        $response->assertSee('Jane Customer', false);
        $response->assertSee('name="role"', false);
        $response->assertSee('assignRoleModalDash'.$this->customer->id, false);
    }

    public function test_admin_users_page_displays_role_assignment_and_modals(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));

        $response->assertOk();
        $response->assertSee('Admins:', false);
        $response->assertSee('Admins Only', false);
        $response->assertSee('Jane Customer', false);
        $response->assertSee('assignRoleModal'.$this->customer->id, false);
    }

    public function test_admin_can_assign_driver_role_to_customer(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.assignRole', $this->customer->id), [
            'role' => 'driver',
            'vehicle_type' => 'motorbike',
            'license_plate' => '2AB-9999',
            'verify_driver' => '1',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertEquals('driver', $this->customer->role);
        $this->assertEquals('motorbike', $this->customer->vehicle_type);
        $this->assertEquals('2AB-9999', $this->customer->license_plate);
        $this->assertNotNull($this->customer->driver_verified_at);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'assign_role',
            'target_id' => $this->customer->id,
        ]);

        // Verify notification dispatched
        $this->assertDatabaseHas('in_app_notifications', [
            'user_id' => $this->customer->id,
            'type' => 'role_updated',
        ]);
    }

    public function test_admin_can_promote_customer_to_admin(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.assignRole', $this->customer->id), [
            'role' => 'admin',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->customer->refresh();
        $this->assertEquals('admin', $this->customer->role);
        $this->assertTrue($this->customer->isAdmin());
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.assignRole', $this->admin->id), [
            'role' => 'customer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->admin->refresh();
        $this->assertEquals('admin', $this->admin->role);
    }

    public function test_non_admin_cannot_assign_roles(): void
    {
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($this->customer)->post(route('admin.users.assignRole', $otherCustomer->id), [
            'role' => 'admin',
        ]);

        $response->assertStatus(302); // Redirected because of role middleware
        $otherCustomer->refresh();
        $this->assertEquals('customer', $otherCustomer->role);
    }
}

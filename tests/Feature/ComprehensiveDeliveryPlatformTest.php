<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Dispute;
use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveDeliveryPlatformTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $driver;

    private User $admin;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@nham247.com',
            'phone' => '+855 12 111 222',
            'role' => 'customer',
        ]);

        $this->driver = User::factory()->create([
            'name' => 'Test Courier',
            'email' => 'driver@nham247.com',
            'phone' => '+855 98 888 777',
            'role' => 'driver',
            'is_online' => true,
            'vehicle_type' => 'motorbike',
            'license_plate' => '1AB-9999',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Chief',
            'email' => 'admin@nham247.com',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Cambodian Kitchen',
            'description' => 'Authentic local cuisine',
        ]);

        $this->product = Product::create([
            'name' => 'Beef Lok Lak',
            'description' => 'Tender stir-fried beef with pepper lime sauce',
            'price' => 6.00,
            'category_id' => $category->id,
        ]);
    }

    public function test_customer_multi_address_management_and_default_toggle(): void
    {
        $response = $this->actingAs($this->customer)->postJson(route('addresses.store'), [
            'label' => 'Home',
            'recipient_name' => 'Test Customer',
            'phone' => '+855 12 111 222',
            'address_line' => 'St 51, BKK1, Phnom Penh',
            'latitude' => 11.5500,
            'longitude' => 104.9200,
            'notes' => 'Ring gate bell',
            'is_default' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('address.label', 'Home');

        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $this->customer->id,
            'label' => 'Home',
            'is_default' => 1,
        ]);

        // Add second address (Work)
        $workResponse = $this->actingAs($this->customer)->postJson(route('addresses.store'), [
            'label' => 'Work Office',
            'recipient_name' => 'Test Customer',
            'phone' => '+855 12 111 222',
            'address_line' => 'Vattanac Tower, Monivong Blvd',
            'latitude' => 11.5720,
            'longitude' => 104.9210,
            'is_default' => true,
        ]);

        $workResponse->assertOk();

        // Previous address Home should now be is_default = 0, Work should be is_default = 1
        $this->assertEquals(2, $this->customer->addresses()->count());
        $this->assertTrue((bool) $this->customer->addresses()->where('label', 'Work Office')->first()->is_default);
        $this->assertFalse((bool) $this->customer->addresses()->where('label', 'Home')->first()->is_default);
    }

    public function test_promo_code_validation_and_discount_calculation(): void
    {
        PromoCode::create([
            'code' => 'NHAM10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'min_order' => 10.00,
            'is_active' => true,
        ]);

        // Below minimum order
        $failResponse = $this->actingAs($this->customer)->postJson(route('promo.apply'), [
            'code' => 'NHAM10',
            'amount' => 5.00,
        ]);
        $failResponse->assertStatus(422);

        // Valid order ($20 -> 10% discount = $2, new total = $18)
        $passResponse = $this->actingAs($this->customer)->postJson(route('promo.apply'), [
            'code' => 'NHAM10',
            'amount' => 20.00,
        ]);
        $passResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('discount_amount', 2)
            ->assertJsonPath('new_total', 18);
    }

    public function test_customer_favorites_toggle(): void
    {
        // Toggle on
        $response = $this->actingAs($this->customer)->postJson(route('favorites.toggle', $this->product->id));
        $response->assertOk()->assertJsonPath('favorited', true);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);

        // Toggle off
        $response2 = $this->actingAs($this->customer)->postJson(route('favorites.toggle', $this->product->id));
        $response2->assertOk()->assertJsonPath('favorited', false);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_in_app_notifications_lifecycle(): void
    {
        InAppNotification::createAlert(
            $this->customer->id,
            null,
            'test_alert',
            'Welcome to 24/7 NHAM!',
            'Thank you for joining our platform.',
            'customer'
        );

        $badgeResponse = $this->actingAs($this->customer)->getJson(route('notifications.unread'));
        $badgeResponse->assertOk()->assertJsonPath('count', 1);

        // Mark all as read
        $markResponse = $this->actingAs($this->customer)->postJson(route('notifications.markAllRead'));
        $markResponse->assertOk()->assertJsonPath('success', true);

        $badgeResponseAfter = $this->actingAs($this->customer)->getJson(route('notifications.unread'));
        $badgeResponseAfter->assertOk()->assertJsonPath('count', 0);
    }

    public function test_in_app_chat_between_customer_and_courier(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'amount' => 12.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_OUT_FOR_DELIVERY,
        ]);

        // Customer sends message
        $sendResp = $this->actingAs($this->customer)->postJson(route('chat.order.send', $order->id), [
            'message' => 'Please leave at security guard desk.',
        ]);
        $sendResp->assertOk()->assertJsonPath('success', true);

        // Courier fetches messages
        $getResp = $this->actingAs($this->driver)->getJson(route('chat.order.messages', $order->id));
        $getResp->assertOk()
            ->assertJsonPath('messages.0.message', 'Please leave at security guard desk.');
    }

    public function test_driver_job_acceptance_and_1_tap_progression(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 15.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PENDING,
            'shipping_address' => 'St 214, Phnom Penh',
        ]);

        // Driver accepts order
        $acceptResp = $this->actingAs($this->driver)->post(route('driver.accept', $order->id));
        $acceptResp->assertRedirect(route('driver.active'));
        $this->assertEquals($this->driver->id, $order->fresh()->driver_id);

        // 1-Tap Picked Up
        $pickupResp = $this->actingAs($this->driver)->post(route('driver.updateStatus', $order->id), [
            'status' => 'picked_up',
        ]);
        $this->assertEquals(Order::DELIVERY_PICKED_UP, $order->fresh()->delivery_status);

        // 1-Tap On the Way
        $routeResp = $this->actingAs($this->driver)->post(route('driver.updateStatus', $order->id), [
            'status' => 'out_for_delivery',
        ]);
        $this->assertEquals(Order::DELIVERY_OUT_FOR_DELIVERY, $order->fresh()->delivery_status);

        // 1-Tap Delivered
        $deliveredResp = $this->actingAs($this->driver)->post(route('driver.updateStatus', $order->id), [
            'status' => 'delivered',
        ]);
        $this->assertEquals(Order::DELIVERY_DELIVERED, $order->fresh()->delivery_status);
        $this->assertNotNull($order->fresh()->delivered_at);

        // Quick Preset Message
        $presetResp = $this->actingAs($this->driver)->post(route('driver.preset', $order->id), [
            'preset' => 'arrived',
        ]);
        $presetResp->assertRedirect();
        $this->assertDatabaseHas('in_app_notifications', [
            'user_id' => $this->customer->id,
            'type' => 'driver_update',
        ]);
    }

    public function test_ratings_and_driver_review(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'amount' => 20.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_DELIVERED,
        ]);

        $reviewResp = $this->actingAs($this->customer)->post(route('order.review.store', $order->id), [
            'food_rating' => 5,
            'driver_rating' => 5,
            'comment' => 'Super fast delivery and hot food!',
        ]);
        $reviewResp->assertRedirect();

        $this->assertDatabaseHas('order_reviews', [
            'order_id' => $order->id,
            'driver_id' => $this->driver->id,
            'driver_rating' => 5,
        ]);

        $this->assertEquals(5.0, $this->driver->fresh()->averageDriverRating());
    }

    public function test_admin_user_management_and_driver_approval(): void
    {
        $unverifiedDriver = User::factory()->create([
            'name' => 'New Courier Candidate',
            'email' => 'newdriver@nham247.com',
            'role' => 'driver',
        ]);

        $this->assertNull($unverifiedDriver->driver_verified_at);

        // Admin verifies courier
        $approveResp = $this->actingAs($this->admin)->post(route('admin.users.approveDriver', $unverifiedDriver->id));
        $approveResp->assertRedirect();
        $this->assertNotNull($unverifiedDriver->fresh()->driver_verified_at);

        // Admin suspends user
        $toggleResp = $this->actingAs($this->admin)->post(route('admin.users.toggle', $unverifiedDriver->id));
        $toggleResp->assertRedirect();
        $this->assertEquals('suspended', $unverifiedDriver->fresh()->status);
    }

    public function test_admin_active_orders_reassign_and_cancel_with_audit_log(): void
    {
        $secondDriver = User::factory()->create([
            'name' => 'Second Driver',
            'email' => 'driver2@nham247.com',
            'role' => 'driver',
            'is_online' => true,
        ]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'driver_id' => $this->driver->id,
            'amount' => 18.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PICKED_UP,
        ]);

        // Reassign
        $reassignResp = $this->actingAs($this->admin)->post(route('admin.orders.reassign', $order->id), [
            'driver_id' => $secondDriver->id,
        ]);
        $reassignResp->assertRedirect();
        $this->assertEquals($secondDriver->id, $order->fresh()->driver_id);

        // Cancel order with reason
        $cancelResp = $this->actingAs($this->admin)->post(route('admin.orders.cancel', $order->id), [
            'reason' => 'Store closed due to heavy rain',
        ]);
        $cancelResp->assertRedirect();
        $this->assertEquals(Order::DELIVERY_CANCELLED, $order->fresh()->delivery_status);
        $this->assertEquals('Store closed due to heavy rain', $order->fresh()->cancellation_reason);

        // Verify AuditLog was recorded
        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $this->admin->id,
            'action' => 'cancel_order',
        ]);
    }

    public function test_admin_broadcast_announcement(): void
    {
        $broadcastResp = $this->actingAs($this->admin)->post(route('admin.broadcast.send'), [
            'title' => 'Flash Sale Alert',
            'message' => '50% OFF drinks all afternoon today!',
            'target_role' => 'all',
        ]);
        $broadcastResp->assertRedirect();

        $this->assertDatabaseHas('in_app_notifications', [
            'type' => 'broadcast',
            'title' => 'Flash Sale Alert',
            'target_role' => 'all',
        ]);
    }

    public function test_dispute_resolution_workflow(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 14.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_DELIVERED,
        ]);

        // Customer opens dispute
        $disputeResp = $this->actingAs($this->customer)->post(route('order.dispute.store', $order->id), [
            'subject' => 'Missing Beverage',
            'description' => 'I did not receive the iced coffee ordered.',
        ]);
        $disputeResp->assertRedirect();

        $dispute = Dispute::where('order_id', $order->id)->first();
        $this->assertNotNull($dispute);
        $this->assertEquals('open', $dispute->status);

        // Admin resolves dispute
        $resolveResp = $this->actingAs($this->admin)->post(route('admin.disputes.resolve', $dispute->id), [
            'status' => 'resolved',
            'resolution_notes' => 'Refund voucher of $2.50 credited to customer.',
        ]);
        $resolveResp->assertRedirect();

        $this->assertEquals('resolved', $dispute->fresh()->status);
        $this->assertDatabaseHas('in_app_notifications', [
            'user_id' => $this->customer->id,
            'type' => 'dispute_update',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTrackingTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private User $otherCustomer;

    private User $admin;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer_test@example.com',
            'role' => 'customer',
        ]);

        $this->otherCustomer = User::factory()->create([
            'name' => 'Other Customer',
            'email' => 'other_customer@example.com',
            'role' => 'customer',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin_test@example.com',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Meals',
            'description' => 'Delicious food and meals',
        ]);

        $this->product = Product::create([
            'name' => 'Fried Rice',
            'description' => 'Delicious wok fried rice',
            'price' => 5.50,
            'category_id' => $category->id,
        ]);
    }

    public function test_customer_can_view_their_order_history(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 11.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PREPARING,
            'delivery_address' => '123 Test St, Apt 4B',
            'customer_phone' => '012345678',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'amount' => 5.50,
        ]);

        // Other customer's order
        $otherOrder = Order::create([
            'user_id' => $this->otherCustomer->id,
            'amount' => 20.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PENDING,
            'delivery_address' => '999 Secret Blvd',
        ]);

        $response = $this->actingAs($this->customer)->get(route('customer.orders.index'));

        $response->assertOk();
        $response->assertSee('Order #'.$order->id);
        $response->assertSee($order->tracking_number);
        $response->assertSee('Fried Rice');
        $response->assertDontSee('999 Secret Blvd');
    }

    public function test_customer_can_view_their_order_tracking_details(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 5.50,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_OUT_FOR_DELIVERY,
            'delivery_address' => '123 Delivery Way',
            'customer_phone' => '0987654321',
        ]);

        $response = $this->actingAs($this->customer)->get(route('customer.orders.show', $order->id));

        $response->assertOk();
        $response->assertSee($order->tracking_number);
        $response->assertSee('On the Way');
        $response->assertSee('123 Delivery Way');
        $response->assertSee('0987654321');
    }

    public function test_customer_cannot_view_other_customers_order_details(): void
    {
        $otherOrder = Order::create([
            'user_id' => $this->otherCustomer->id,
            'amount' => 15.00,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PENDING,
            'delivery_address' => 'Private Lane',
        ]);

        $response = $this->actingAs($this->customer)->get(route('customer.orders.show', $otherOrder->id));

        $response->assertNotFound();
    }

    public function test_public_shipment_tracking_by_tracking_number(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 5.50,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PREPARING,
            'delivery_address' => 'Street 200, House 5',
        ]);

        $response = $this->get(route('order.track', ['tracking' => $order->tracking_number]));

        $response->assertOk();
        $response->assertSee($order->tracking_number);
        $response->assertSee('Preparing');
    }

    public function test_admin_can_update_delivery_status(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 5.50,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_PENDING,
            'delivery_address' => 'Sample Street',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.orders.updateDeliveryStatus', $order->id), [
            'delivery_status' => 'out_for_delivery',
        ]);

        $response->assertRedirect();
        $this->assertEquals('out_for_delivery', $order->fresh()->delivery_status);

        // Advance to delivered
        $this->actingAs($this->admin)->post(route('admin.orders.updateDeliveryStatus', $order->id), [
            'delivery_status' => 'delivered',
        ]);

        $this->assertEquals('delivered', $order->fresh()->delivery_status);
        $this->assertNotNull($order->fresh()->delivered_at);
    }

    public function test_api_can_track_shipment_by_tracking_number(): void
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'amount' => 5.50,
            'status' => Order::STATUS_PAID,
            'delivery_status' => Order::DELIVERY_OUT_FOR_DELIVERY,
            'delivery_address' => 'API Street 1',
        ]);

        $response = $this->getJson('/api/track/'.$order->tracking_number);

        $response->assertOk()
            ->assertJsonPath('tracking.tracking_number', $order->tracking_number)
            ->assertJsonPath('tracking.delivery_status', 'out_for_delivery')
            ->assertJsonPath('tracking.delivery_step', 4);
    }
}

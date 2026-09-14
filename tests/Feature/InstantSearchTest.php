<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstantSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Product $product;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin_search@example.com',
            'role' => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Coffee & Drinks',
            'description' => 'Cold drinks and beverages',
        ]);

        $this->product = Product::create([
            'name' => 'Cappuccino Ice',
            'price' => 2.50,
            'description' => 'Rich espresso with chilled foam',
            'category_id' => $category->id,
            'image' => 'cappuccino.png',
        ]);

        $this->order = Order::create([
            'user_id' => $this->admin->id,
            'amount' => 2.50,
            'status' => Order::STATUS_APPROVED,
            'delivery_status' => Order::DELIVERY_CONFIRMED,
            'tracking_number' => 'TRK-2026-TEST01',
        ]);
    }

    public function test_navbar_search_returns_products_for_single_character(): void
    {
        $response = $this->getJson('/navbar-search?title=c');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'price',
                'image',
                'url',
                'admin_url',
                'edit_url',
            ],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data, 'Search for single character "c" should return matching products');

        foreach ($data as $item) {
            $this->assertStringContainsStringIgnoringCase('c', $item['title']);
        }
    }

    public function test_navbar_search_returns_empty_when_no_keyword(): void
    {
        $response = $this->getJson('/navbar-search?title=');

        $response->assertStatus(200);
        $this->assertEquals([], $response->json());
    }

    public function test_frontend_search_page_loads_with_keyword(): void
    {
        $response = $this->get('/search?keyword=c');

        $response->assertStatus(200);
        $response->assertSee('mainSearchInput');
        $response->assertSee('mainSearchResults');
    }

    public function test_order_tracking_ajax_returns_live_matches(): void
    {
        $response = $this->getJson('/track?tracking=TRK');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'tracking_number',
                'amount',
                'status',
                'date',
                'url',
            ],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data);
        $this->assertEquals('TRK-2026-TEST01', $data[0]['tracking_number']);
    }

    public function test_admin_pages_contain_instant_filter_inputs(): void
    {
        // Product list table
        $response = $this->actingAs($this->admin)->get('/product');
        $response->assertStatus(200);
        $response->assertSee('productTableFilter');

        // Order list table
        $response = $this->actingAs($this->admin)->get(route('admin.order'));
        $response->assertStatus(200);
        $response->assertSee('orderTableFilter');

        // Category list table
        $response = $this->actingAs($this->admin)->get('/category');
        $response->assertStatus(200);
        $response->assertSee('categoryTableFilter');

        // User list table
        $response = $this->actingAs($this->admin)->get('/admin/users');
        $response->assertStatus(200);
        $response->assertSee('userTableFilter');
    }
}

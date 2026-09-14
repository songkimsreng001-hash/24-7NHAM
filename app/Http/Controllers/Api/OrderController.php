<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class OrderController extends Controller
{
    /**
     * Get all orders for the authenticated user.
     */
    public function index(Request $request)
    {
        try {
            $orders = Order::with(['orderItems.product', 'driver'])
                ->where('user_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'message' => 'Orders fetched',
                'orders' => $orders,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch orders',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a single order with its items and delivery status.
     */
    public function show(Request $request, $id)
    {
        try {
            $order = Order::with(['orderItems.product', 'driver'])
                ->where('user_id', $request->user()->id)
                ->findOrFail($id);

            return response()->json([
                'message' => 'Order fetched',
                'order' => $order,
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Order not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Public shipment tracking endpoint.
     */
    public function track(Request $request, $trackingNumber)
    {
        try {
            $order = Order::with(['orderItems.product', 'driver'])
                ->where('tracking_number', $trackingNumber)
                ->first();

            if (! $order && is_numeric($trackingNumber)) {
                $order = Order::with(['orderItems.product', 'driver'])->find((int) $trackingNumber);
            }

            if (! $order) {
                return response()->json([
                    'message' => 'Shipment not found for tracking number: '.$trackingNumber,
                ], 404);
            }

            return response()->json([
                'message' => 'Shipment tracked successfully',
                'tracking' => [
                    'order_id' => $order->id,
                    'tracking_number' => $order->tracking_number,
                    'delivery_status' => $order->delivery_status,
                    'status_label' => $order->deliveryStatusLabel(),
                    'delivery_step' => $order->deliveryStep(),
                    'delivery_address' => $order->delivery_address,
                    'estimated_delivery_at' => $order->estimated_delivery_at,
                    'delivered_at' => $order->delivered_at,
                    'driver' => $order->driver ? [
                        'name' => $order->driver->name,
                        'email' => $order->driver->email,
                    ] : null,
                    'items_count' => $order->orderItems->count(),
                    'amount' => $order->amount,
                    'created_at' => $order->created_at,
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Tracking lookup failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Place a new order (called after Stripe payment success on Flutter/mobile).
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'payment_intent_id' => 'required|string',
                'delivery_address' => 'nullable|string|max:500',
                'customer_phone' => 'nullable|string|max:30',
                'delivery_notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Calculate total amount from real DB prices (never trust client prices)
            $totalAmount = 0;
            $resolvedItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $lineTotal = $product->price * $item['quantity'];
                $totalAmount += $lineTotal;

                $resolvedItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'line_total' => $lineTotal,
                ];
            }

            // Create Order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'amount' => $totalAmount,
                'status' => Order::STATUS_PAID,
                'payment_intent_id' => $request->payment_intent_id,
                'delivery_status' => Order::DELIVERY_PENDING,
                'delivery_address' => $request->delivery_address,
                'customer_phone' => $request->customer_phone,
                'delivery_notes' => $request->delivery_notes,
                'estimated_delivery_at' => now()->addMinutes(45),
            ]);

            // Create Order Items
            foreach ($resolvedItems as $ri) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $ri['product']->id,
                    'quantity' => $ri['quantity'],
                    'amount' => $ri['unit_price'],
                ]);
            }

            return response()->json([
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'tracking_number' => $order->tracking_number,
                'delivery_status' => $order->delivery_status,
                'total_amount' => $totalAmount,
            ], 201);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

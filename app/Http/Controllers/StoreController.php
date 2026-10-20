<?php

namespace App\Http\Controllers;

use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Throwable;

class StoreController extends Controller
{
    public function cart()
    {
        return view('frontend.cart');
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $requestedQuantity = max(1, min(99, (int) $request->input('quantity', 1)));

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min(99, $cart[$id]['quantity'] + $requestedQuantity);
        } else {
            $cart[$id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $requestedQuantity,
                'price' => $product->price,
                'image' => $product->image,
                'image_url' => $product->image_url,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        $request->validate(['id' => ['required'], 'quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = (int) $request->quantity;
            session()->put('cart', $cart);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function remove(Request $request)
    {
        $request->validate(['id' => ['required']]);
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $totalAmount = collect($cart)->sum(fn ($item) => (float) $item['price'] * (int) $item['quantity']);

        $addresses = $user->addresses()->orderBy('is_default', 'desc')->get();
        $defaultAddress = $addresses->where('is_default', true)->first() ?: $addresses->first();

        return view('frontend.checkout', [
            'cartItems' => $cart,
            'totalAmount' => $totalAmount,
            'stripeKey' => config('services.stripe.key'),
            'user' => $user,
            'addresses' => $addresses,
            'defaultAddress' => $defaultAddress,
            'phoneMissing' => empty($user->phone),
        ]);
    }

    public function completeCheckout(Request $request)
    {
        $paymentMethod = $request->input('payment_method', 'card');

        // Sanitize phone to strictly digits
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->customer_phone);
        $request->merge(['customer_phone' => $cleanPhone]);

        $rules = [
            'payment_method' => ['required', 'in:card,cod'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'customer_phone' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
            'promo_code' => ['nullable', 'string', 'max:50'],
            'delivery_latitude' => ['nullable', 'numeric'],
            'delivery_longitude' => ['nullable', 'numeric'],
            'distance_km' => ['nullable', 'numeric'],
            'eta_minutes' => ['nullable', 'integer'],
        ];

        if ($paymentMethod === 'card') {
            $rules['payment_intent_id'] = ['required', 'string'];
        }

        $request->validate($rules, [
            'customer_phone.regex' => 'Contact phone number must be exactly 10 digits (numbers only).',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();

        // Save phone to profile if user did not have one
        if (empty($user->phone)) {
            $user->update(['phone' => $request->customer_phone]);
        }

        try {
            $rawSubtotal = collect($cart)->sum(fn ($item) => (float) $item['price'] * (int) $item['quantity']);
            $discountAmount = 0.0;
            $appliedPromo = null;

            if ($request->filled('promo_code')) {
                $promo = PromoCode::where('code', strtoupper(trim($request->promo_code)))->first();
                if ($promo && $promo->isValid($rawSubtotal)) {
                    $discountAmount = $promo->calculateDiscount($rawSubtotal);
                    $appliedPromo = $promo->code;
                }
            }

            $finalTotal = max(0, round($rawSubtotal - $discountAmount, 2));

            $paymentIntentId = null;

            if ($paymentMethod === 'card') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $intent = PaymentIntent::retrieve($request->payment_intent_id);

                $expectedCents = (int) round($finalTotal * 100);

                if ($intent->status !== 'succeeded' || (int) $intent->amount !== $expectedCents || (string) ($intent->metadata->user_id ?? '') !== (string) $user->id) {
                    return redirect()->route('cart.checkout')->with('error', 'Payment could not be verified.');
                }

                $paymentIntentId = $intent->id;
                $orderStatus = Order::STATUS_PAID;
            } else {
                // Cash on Delivery
                $orderStatus = Order::STATUS_PAID; // Registered as pending fulfillment
            }

            $etaMins = (int) ($request->input('eta_minutes') ?: 35);

            $order = Order::create([
                'user_id' => $user->id,
                'amount' => $finalTotal,
                'status' => $orderStatus,
                'payment_intent_id' => $paymentIntentId,
                'payment_method' => $paymentMethod,
                'discount_amount' => $discountAmount,
                'promo_code' => $appliedPromo,
                'delivery_status' => Order::DELIVERY_PENDING,
                'delivery_address' => $request->input('delivery_address'),
                'delivery_latitude' => $request->input('delivery_latitude'),
                'delivery_longitude' => $request->input('delivery_longitude'),
                'distance_km' => $request->input('distance_km'),
                'eta_minutes' => $etaMins,
                'customer_phone' => $request->input('customer_phone'),
                'delivery_notes' => $request->input('delivery_notes'),
                'estimated_delivery_at' => now()->addMinutes($etaMins),
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['price'],
                ]);
            }

            session()->forget('cart');

            // Send in-app notification to Customer
            InAppNotification::createAlert(
                $user->id,
                $order->id,
                'order_placed',
                'Order Placed Successfully!',
                "Order #{$order->id} ({$order->tracking_number}) has been placed. We are preparing your items!",
                'customer',
                'bi-bag-check-fill'
            );

            // Send in-app notification to Admin & Drivers
            InAppNotification::createAlert(
                null,
                $order->id,
                'new_order',
                'New Delivery Job Available',
                "New order #{$order->id} for \${$order->amount} is ready for dispatch.",
                'driver',
                'bi-bell-fill'
            );

            return redirect()->route('customer.orders.show', $order->id)->with(
                'success',
                "Your order #{$order->id} has been placed! Tracking Number: {$order->tracking_number}"
            );
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('cart.checkout')->with('error', 'We could not process your order. '.$e->getMessage());
        }
    }
}

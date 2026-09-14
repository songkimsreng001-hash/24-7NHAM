<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Create a Stripe PaymentIntent from the cart items sent by Flutter.
     *
     * Flutter sends:
     * {
     *   "items": [
     *     { "product_id": 1, "quantity": 2 },
     *     { "product_id": 3, "quantity": 1 }
     *   ]
     * }
     *
     * Returns: { "clientSecret": "pi_xxx_secret_xxx", "amount": 2500 }
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $cart = $request->session()->get('cart', []);

            if (empty($cart)) {
                return response()->json(['message' => 'Your cart is empty.'], 422);
            }

            $totalAmount = 0;
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = max(1, (int) $item['quantity']);
                $totalAmount += (float) $product->price * $quantity;
            }

            $amountInCents = (int) round($totalAmount * 100);
            if ($amountInCents < 50) {
                return response()->json(['message' => 'Order amount is too small.'], 422);
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => ['user_id' => $request->user()->id],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amountInCents,
                'currency' => 'usd',
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Payment intent creation failed.'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
            $validator = Validator::make($request->all(), [
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // FIX: Calculate amount server-side — never trust client amounts
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];
            }

            $amountInCents = (int) round($totalAmount * 100);

            if ($amountInCents < 50) {   // Stripe minimum is $0.50
                return response()->json([
                    'message' => 'Order amount is too small',
                ], 422);
            }

            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd',
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => [
                    'user_id' => $request->user()->id,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'amount' => $amountInCents,
                'currency' => 'usd',
            ]);

        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Payment intent creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

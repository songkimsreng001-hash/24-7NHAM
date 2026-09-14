<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
        ]);

        $code = strtoupper(trim($request->code));
        $subtotal = (float) $request->amount;

        $promo = PromoCode::where('code', $code)->first();

        if (! $promo) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid promo code. Please check and try again.',
            ], 422);
        }

        if (! $promo->isValid($subtotal)) {
            $msg = ! $promo->is_active ? 'This promo code is no longer active.' :
                   ($promo->expires_at && $promo->expires_at->isPast() ? 'This promo code has expired.' :
                   "Minimum order of \${$promo->min_order} required to use this code.");

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 422);
        }

        $discount = $promo->calculateDiscount($subtotal);
        $newTotal = max(0, round($subtotal - $discount, 2));

        return response()->json([
            'success' => true,
            'message' => "Promo code '{$promo->code}' applied successfully!",
            'promo_code' => $promo->code,
            'discount_amount' => $discount,
            'discount_label' => $promo->discount_type === 'percent' ? "{$promo->discount_value}% OFF" : "-\${$discount}",
            'new_total' => $newTotal,
        ]);
    }
}

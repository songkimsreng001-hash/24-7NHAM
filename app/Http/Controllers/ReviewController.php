<?php

namespace App\Http\Controllers;

use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\OrderReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'food_rating' => 'required|integer|min:1|max:5',
            'driver_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $order = Order::where('id', $orderId)->where('user_id', $user->id)->firstOrFail();

        if ($order->review()->exists()) {
            return redirect()->back()->with('error', 'You have already submitted a review for this order.');
        }

        $review = OrderReview::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'driver_id' => $order->driver_id,
            'food_rating' => (int) $request->food_rating,
            'driver_rating' => (int) $request->driver_rating,
            'comment' => $request->comment,
        ]);

        // Notify Driver of the feedback
        if ($order->driver_id) {
            InAppNotification::createAlert(
                $order->driver_id,
                $order->id,
                'review_received',
                "New {$review->driver_rating}★ Rating Received",
                "A customer reviewed your delivery for Order #{$order->id}.",
                'driver',
                'bi-star-fill'
            );
        }

        return redirect()->back()->with('success', 'Thank you for your rating and feedback!');
    }
}

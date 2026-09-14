<?php

namespace App\Http\Controllers;

use App\Models\InAppNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Display order history for the authenticated customer.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'all');

        $baseQuery = Order::with(['orderItems.product', 'driver', 'review'])
            ->where('user_id', $user->id);

        $totalCount = (clone $baseQuery)->count();
        $activeCount = (clone $baseQuery)->whereIn('delivery_status', [
            Order::DELIVERY_PENDING,
            Order::DELIVERY_CONFIRMED,
            Order::DELIVERY_PREPARING,
            Order::DELIVERY_PICKED_UP,
            Order::DELIVERY_OUT_FOR_DELIVERY,
        ])->count();
        $deliveredCount = (clone $baseQuery)->where('delivery_status', Order::DELIVERY_DELIVERED)->count();
        $cancelledCount = (clone $baseQuery)->where('delivery_status', Order::DELIVERY_CANCELLED)->count();

        $query = clone $baseQuery;

        if ($tab === 'active') {
            $query->whereIn('delivery_status', [
                Order::DELIVERY_PENDING,
                Order::DELIVERY_CONFIRMED,
                Order::DELIVERY_PREPARING,
                Order::DELIVERY_PICKED_UP,
                Order::DELIVERY_OUT_FOR_DELIVERY,
            ]);
        } elseif ($tab === 'delivered') {
            $query->where('delivery_status', Order::DELIVERY_DELIVERED);
        } elseif ($tab === 'cancelled') {
            $query->where('delivery_status', Order::DELIVERY_CANCELLED);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('frontend.orders.index', compact(
            'orders',
            'tab',
            'totalCount',
            'activeCount',
            'deliveredCount',
            'cancelledCount'
        ));
    }

    /**
     * Show detailed order information and visual delivery tracking for an order.
     */
    public function show($id)
    {
        $user = Auth::user();
        $orderId = $id instanceof Order ? $id->id : $id;

        $orderQuery = Order::with(['orderItems.product', 'driver', 'user', 'review', 'dispute'])->where('id', $orderId);

        // Regular users can only view their own orders; admins can view any order
        if ($user->role !== 'admin') {
            $orderQuery->where('user_id', $user->id);
        }

        $order = $orderQuery->firstOrFail();

        return view('frontend.orders.show', compact('order'));
    }

    /**
     * Re-order shortcut: repopulate cart from past order items and jump to checkout.
     */
    public function reorder($id)
    {
        $user = Auth::user();
        $orderId = $id instanceof Order ? $id->id : $id;
        $order = Order::with('orderItems.product')->where('user_id', $user->id)->findOrFail($orderId);

        $cart = session()->get('cart', []);

        foreach ($order->orderItems as $item) {
            $product = $item->product;
            if (! $product) {
                continue;
            }

            $pId = $product->id;
            if (isset($cart[$pId])) {
                $cart[$pId]['quantity'] = min(99, $cart[$pId]['quantity'] + $item->quantity);
            } else {
                $cart[$pId] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                    'image' => $product->image,
                ];
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.checkout')->with('success', "Items from Order #{$order->id} added to cart! Complete your delivery checkout below.");
    }

    /**
     * Cancel order by customer if still pending/confirmed
     */
    public function cancel(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $user = Auth::user();
        $orderId = $id instanceof Order ? $id->id : $id;
        $order = Order::where('id', $orderId)->where('user_id', $user->id)->firstOrFail();

        if (! $order->canBeCancelled()) {
            return redirect()->back()->with('error', 'This order is already being prepared or on the way and cannot be cancelled automatically.');
        }

        $order->update([
            'status' => Order::STATUS_REJECTED,
            'delivery_status' => Order::DELIVERY_CANCELLED,
            'cancellation_reason' => $request->reason,
        ]);

        // Alert driver if one was assigned
        if ($order->driver_id) {
            InAppNotification::createAlert(
                $order->driver_id,
                $order->id,
                'order_cancelled',
                "Order #{$order->id} Cancelled by Customer",
                "Reason: {$request->reason}",
                'driver',
                'bi-x-octagon-fill'
            );
        }

        return redirect()->back()->with('success', "Order #{$order->id} has been cancelled.");
    }

    /**
     * Quick tracking search by tracking number or order ID.
     */
    public function track(Request $request, $trackingNumber = null)
    {
        $trackingQuery = trim($request->input('tracking', $trackingNumber ?? ''));
        $order = null;
        $searched = false;

        if ($request->expectsJson() || $request->ajax()) {
            if (empty($trackingQuery)) {
                return response()->json([]);
            }
            $matches = Order::where('tracking_number', 'like', '%'.$trackingQuery.'%')
                ->orWhere('id', 'like', $trackingQuery.'%')
                ->limit(6)
                ->get()
                ->map(fn ($o) => [
                    'id' => $o->id,
                    'tracking_number' => $o->tracking_number ?? ('Order #'.$o->id),
                    'amount' => number_format((float) $o->amount, 2),
                    'status' => ucfirst(str_replace('_', ' ', $o->delivery_status ?? 'pending')),
                    'date' => $o->created_at ? $o->created_at->format('M d, Y') : '',
                    'url' => route('order.track.search', $o->tracking_number ?: $o->id),
                ]);

            return response()->json($matches);
        }

        if (! empty($trackingQuery)) {
            $searched = true;

            $query = Order::with(['orderItems.product', 'driver', 'user', 'review'])
                ->where('tracking_number', $trackingQuery);

            if (is_numeric($trackingQuery)) {
                $query->orWhere('id', (int) $trackingQuery);
            }

            $order = $query->first();
        }

        return view('frontend.orders.track', compact('order', 'trackingQuery', 'searched'));
    }
}

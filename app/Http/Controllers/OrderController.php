<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product', 'driver'])
            ->orderBy('created_at', 'desc')
            ->get();

        $drivers = User::where('role', 'driver')->orderBy('name')->get();

        return view('backend.order', compact('orders', 'drivers'));
    }

    public function approve($id)
    {
        $order = Order::findOrFail($id);
        $order->status = Order::STATUS_APPROVED;
        if ($order->delivery_status === Order::DELIVERY_PENDING) {
            $order->delivery_status = Order::DELIVERY_PREPARING;
        }
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->id} has been approved.");
    }

    public function reject($id)
    {
        $order = Order::findOrFail($id);
        $order->status = Order::STATUS_REJECTED;
        $order->delivery_status = Order::DELIVERY_CANCELLED;
        $order->save();

        return redirect()->back()->with('success', "Order #{$order->id} has been rejected.");
    }

    public function updateDeliveryStatus(Request $request, $id)
    {
        $request->validate([
            'delivery_status' => ['required', 'string', 'in:pending,preparing,out_for_delivery,delivered,cancelled'],
        ]);

        $order = Order::findOrFail($id);
        $order->delivery_status = $request->delivery_status;

        if ($request->delivery_status === Order::DELIVERY_DELIVERED) {
            $order->delivered_at = now();
            if ($order->status == Order::STATUS_PAID) {
                $order->status = Order::STATUS_APPROVED;
            }
        } elseif ($request->delivery_status === Order::DELIVERY_CANCELLED) {
            $order->status = Order::STATUS_REJECTED;
        }

        $order->save();

        return redirect()->back()->with('success', "Delivery status for Order #{$order->id} updated to {$order->deliveryStatusLabel()}.");
    }

    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => ['nullable', 'exists:users,id'],
        ]);

        $order = Order::findOrFail($id);
        $order->driver_id = $request->driver_id ?: null;

        // If driver assigned and order is still preparing, automatically advance to out for delivery
        if ($order->driver_id && in_array($order->delivery_status, [Order::DELIVERY_PENDING, Order::DELIVERY_PREPARING], true)) {
            $order->delivery_status = Order::DELIVERY_OUT_FOR_DELIVERY;
        }

        $order->save();

        return redirect()->back()->with('success', "Driver assignment updated for Order #{$order->id}.");
    }
}

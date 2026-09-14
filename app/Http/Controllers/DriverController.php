<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\InAppNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Driver Dashboard
     */
    public function dashboard()
    {
        $driver = Auth::user();

        // Active delivery assigned to driver
        $activeOrder = Order::with(['orderItems.product', 'user'])
            ->where('driver_id', $driver->id)
            ->whereIn('delivery_status', [
                Order::DELIVERY_CONFIRMED,
                Order::DELIVERY_PREPARING,
                Order::DELIVERY_PICKED_UP,
                Order::DELIVERY_OUT_FOR_DELIVERY,
            ])
            ->first();

        // Available orders looking for driver (pending orders without driver)
        $availableOrders = Order::with(['orderItems.product', 'user'])
            ->whereNull('driver_id')
            ->whereIn('delivery_status', [Order::DELIVERY_PENDING, Order::DELIVERY_CONFIRMED, Order::DELIVERY_PREPARING])
            ->where('status', '!=', Order::STATUS_REJECTED)
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        // Driver stats
        $todayDeliveries = Order::where('driver_id', $driver->id)
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->whereDate('delivered_at', today())
            ->count();

        // Standard driver earnings: $2.50 base + 10% of order amount
        $todayEarnings = Order::where('driver_id', $driver->id)
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->whereDate('delivered_at', today())
            ->get()
            ->sum(fn ($o) => 2.50 + round($o->amount * 0.10, 2));

        $totalCompleted = $driver->completedDeliveriesCount();
        $averageRating = $driver->averageDriverRating();

        return view('driver.dashboard', compact(
            'driver',
            'activeOrder',
            'availableOrders',
            'todayDeliveries',
            'todayEarnings',
            'totalCompleted',
            'averageRating'
        ));
    }

    /**
     * Accept a delivery job
     */
    public function acceptOrder($id)
    {
        $driver = Auth::user();

        if (! $driver->is_online) {
            return redirect()->back()->with('error', 'Please switch your status to Online before accepting orders.');
        }

        $order = Order::where('id', $id)->whereNull('driver_id')->firstOrFail();

        $order->update([
            'driver_id' => $driver->id,
            'driver_accepted_at' => now(),
            'delivery_status' => Order::DELIVERY_CONFIRMED,
        ]);

        // Notify Customer
        InAppNotification::createAlert(
            $order->user_id,
            $order->id,
            'driver_accepted',
            'Driver Assigned to Your Order!',
            "{$driver->name} has accepted your order and is heading to the store.",
            'customer',
            'bi-person-check-fill'
        );

        return redirect()->route('driver.active')->with('success', "You have accepted Order #{$order->id}!");
    }

    /**
     * Active delivery view
     */
    public function activeDelivery()
    {
        $driver = Auth::user();

        $order = Order::with(['orderItems.product', 'user', 'messages.sender'])
            ->where('driver_id', $driver->id)
            ->whereIn('delivery_status', [
                Order::DELIVERY_CONFIRMED,
                Order::DELIVERY_PREPARING,
                Order::DELIVERY_PICKED_UP,
                Order::DELIVERY_OUT_FOR_DELIVERY,
            ])
            ->first();

        return view('driver.active', compact('driver', 'order'));
    }

    /**
     * Update delivery status in real-time
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:picked_up,out_for_delivery,delivered',
        ]);

        $driver = Auth::user();
        $order = Order::where('id', $id)->where('driver_id', $driver->id)->firstOrFail();

        $newStatus = $request->status;

        if ($newStatus === 'picked_up') {
            $order->update([
                'delivery_status' => Order::DELIVERY_PICKED_UP,
                'picked_up_at' => now(),
            ]);

            InAppNotification::createAlert(
                $order->user_id,
                $order->id,
                'order_picked_up',
                'Order Picked Up!',
                "{$driver->name} has picked up your food from 24/7 NHAM.",
                'customer',
                'bi-bag-check-fill'
            );

        } elseif ($newStatus === 'out_for_delivery') {
            $order->update([
                'delivery_status' => Order::DELIVERY_OUT_FOR_DELIVERY,
            ]);

            InAppNotification::createAlert(
                $order->user_id,
                $order->id,
                'driver_en_route',
                'Courier is On The Way!',
                "{$driver->name} is on the road delivering to your address.",
                'customer',
                'bi-bicycle'
            );

        } elseif ($newStatus === 'delivered') {
            $order->update([
                'delivery_status' => Order::DELIVERY_DELIVERED,
                'delivered_at' => now(),
                'status' => Order::STATUS_APPROVED,
            ]);

            InAppNotification::createAlert(
                $order->user_id,
                $order->id,
                'order_delivered',
                'Order Delivered Successfully!',
                "Your order has been delivered by {$driver->name}. Please enjoy and leave a review!",
                'customer',
                'bi-check2-circle'
            );
        }

        return redirect()->back()->with('success', "Order #{$order->id} updated to ".$order->deliveryStatusLabel().'.');
    }

    /**
     * Quick message presets to customer
     */
    public function sendQuickMessage(Request $request, $id)
    {
        $request->validate([
            'preset' => 'required|string|max:200',
        ]);

        $driver = Auth::user();
        $order = Order::where('id', $id)->where('driver_id', $driver->id)->firstOrFail();

        $text = match ($request->preset) {
            '5_mins' => "Hi {$order->user->name}, I am about 5 minutes away from your location!",
            'arrived' => "Hi {$order->user->name}, I have arrived outside your address. Please meet me to receive your order!",
            'traffic' => 'Hi, there is some road traffic on the way, but I will be there as soon as possible!',
            'gate' => 'I am at the front gate / entrance.',
            default => $request->preset,
        };

        // Create ChatMessage
        ChatMessage::create([
            'order_id' => $order->id,
            'sender_id' => $driver->id,
            'recipient_id' => $order->user_id,
            'channel' => 'driver_customer',
            'message' => $text,
            'is_read' => false,
        ]);

        // Push persistent in-app alert
        InAppNotification::createAlert(
            $order->user_id,
            $order->id,
            'driver_update',
            'Driver Update',
            $text,
            'customer',
            'bi-chat-left-text-fill'
        );

        return redirect()->back()->with('success', 'Update sent to customer.');
    }

    /**
     * Toggle availability (Online/Offline)
     */
    public function toggleAvailability()
    {
        $driver = Auth::user();
        $driver->update(['is_online' => ! $driver->is_online]);

        $status = $driver->is_online ? 'Online' : 'Offline';

        return redirect()->back()->with('success', "Your availability status is now {$status}.");
    }

    /**
     * Driver Earnings & History
     */
    public function earnings()
    {
        $driver = Auth::user();

        $deliveredOrders = Order::with(['orderItems.product', 'user', 'review'])
            ->where('driver_id', $driver->id)
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->orderBy('delivered_at', 'desc')
            ->paginate(15);

        $todayEarnings = Order::where('driver_id', $driver->id)
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->whereDate('delivered_at', today())
            ->get()
            ->sum(fn ($o) => 2.50 + round($o->amount * 0.10, 2));

        $weekEarnings = Order::where('driver_id', $driver->id)
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->where('delivered_at', '>=', now()->startOfWeek())
            ->get()
            ->sum(fn ($o) => 2.50 + round($o->amount * 0.10, 2));

        $totalEarnings = Order::where('driver_id', $driver->id)
            ->where('delivery_status', Order::DELIVERY_DELIVERED)
            ->get()
            ->sum(fn ($o) => 2.50 + round($o->amount * 0.10, 2));

        return view('driver.earnings', compact('driver', 'deliveredOrders', 'todayEarnings', 'weekEarnings', 'totalEarnings'));
    }

    /**
     * Driver Profile
     */
    public function profile()
    {
        $driver = Auth::user();
        $reviews = $driver->driverReviews()->with('user')->orderBy('created_at', 'desc')->take(10)->get();

        return view('driver.profile', compact('driver', 'reviews'));
    }

    public function updateProfile(Request $request)
    {
        $driver = Auth::user();

        $request->validate([
            'phone' => ['required', 'string', 'max:25', 'unique:users,phone,'.$driver->id],
            'vehicle_type' => ['required', 'string', 'in:motorbike,bicycle,car,scooter'],
            'license_plate' => ['required', 'string', 'max:50'],
        ]);

        $driver->update([
            'phone' => $request->phone,
            'vehicle_type' => $request->vehicle_type,
            'license_plate' => $request->license_plate,
        ]);

        return redirect()->back()->with('success', 'Driver profile updated successfully.');
    }
}

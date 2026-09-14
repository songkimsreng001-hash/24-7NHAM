<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dispute;
use App\Models\InAppNotification;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPortalController extends Controller
{
    /**
     * User Management (Customers & Drivers)
     */
    public function users(Request $request)
    {
        $role = $request->query('role', 'all');
        $query = User::orderBy('created_at', 'desc');

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        $users = $query->paginate(15);
        $totalCustomers = User::whereIn('role', ['customer', 'user', null])->count();
        $totalDrivers = User::where('role', 'driver')->count();
        $totalAdmins = User::where('role', 'admin')->count();

        return view('backend.users.index', compact('users', 'role', 'totalCustomers', 'totalDrivers', 'totalAdmins'));
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot suspend your own admin account.');
        }

        $newStatus = $user->status === 'suspended' ? 'active' : 'suspended';
        $user->update(['status' => $newStatus]);

        AuditLog::log(Auth::id(), 'toggle_user_status', 'User', $user->id, "Status changed to {$newStatus}");

        return redirect()->back()->with('success', "User '{$user->name}' is now {$newStatus}.");
    }

    public function approveDriver($id)
    {
        $driver = User::where('role', 'driver')->findOrFail($id);
        $driver->update([
            'driver_verified_at' => now(),
            'status' => 'active',
        ]);

        AuditLog::log(Auth::id(), 'approve_driver', 'User', $driver->id, 'Driver verified and approved');

        InAppNotification::createAlert(
            $driver->id,
            null,
            'driver_approved',
            'Account Approved!',
            'Congratulations! Your driver account has been verified. You can now accept deliveries.',
            'driver',
            'bi-patch-check-fill'
        );

        return redirect()->back()->with('success', "Driver '{$driver->name}' has been verified and approved.");
    }

    /**
     * Assign or update user role (Customer, Courier/Driver, Admin)
     */
    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|string|in:admin,driver,customer,user',
            'vehicle_type' => 'nullable|string|in:motorbike,bicycle,car',
            'license_plate' => 'nullable|string|max:50',
            'verify_driver' => 'nullable',
        ]);

        $user = User::findOrFail($id);
        $oldRole = $user->role ?: 'customer';
        $newRole = $request->role === 'user' ? 'customer' : $request->role;

        // Prevent self-lockout: Current logged-in admin cannot remove their own admin role
        if ($user->id === Auth::id() && $newRole !== 'admin') {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot remove your own admin privileges to prevent account lockout.',
                ], 403);
            }

            return redirect()->back()->with('error', 'You cannot remove your own admin privileges to prevent account lockout.');
        }

        $updateData = ['role' => $newRole];

        // Specific setup when assigning to driver
        if ($newRole === 'driver') {
            if ($request->filled('vehicle_type')) {
                $updateData['vehicle_type'] = $request->vehicle_type;
            } elseif (empty($user->vehicle_type)) {
                $updateData['vehicle_type'] = 'motorbike';
            }

            if ($request->filled('license_plate')) {
                $updateData['license_plate'] = $request->license_plate;
            }

            if ($request->boolean('verify_driver') || $request->has('verify_driver')) {
                $updateData['driver_verified_at'] = now();
                $updateData['status'] = 'active';
            }
        }

        $user->update($updateData);

        $roleLabels = [
            'admin' => 'Administrator',
            'driver' => 'Courier / Driver',
            'customer' => 'Customer',
        ];
        $roleDisplay = $roleLabels[$newRole] ?? ucfirst($newRole);

        // Audit Logging
        AuditLog::log(
            Auth::id(),
            'assign_role',
            'User',
            $user->id,
            "Changed role from '{$oldRole}' to '{$newRole}' for user {$user->name} ({$user->email})"
        );

        // In-App Notification to User
        InAppNotification::createAlert(
            $user->id,
            null,
            'role_updated',
            'Account Role Updated',
            "Your account role has been updated to {$roleDisplay} by an administrator.",
            $newRole,
            'bi-shield-check'
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Role for '{$user->name}' updated to {$roleDisplay}.",
                'role' => $newRole,
                'roleDisplay' => $roleDisplay,
                'user' => $user->fresh(),
            ]);
        }

        return redirect()->back()->with('success', "Role for '{$user->name}' has been successfully changed to {$roleDisplay}.");
    }

    /**
     * Active Orders Dashboard
     */
    public function activeOrders()
    {
        $activeOrders = Order::with(['user', 'driver', 'orderItems.product'])
            ->whereIn('delivery_status', [
                Order::DELIVERY_PENDING,
                Order::DELIVERY_CONFIRMED,
                Order::DELIVERY_PREPARING,
                Order::DELIVERY_PICKED_UP,
                Order::DELIVERY_OUT_FOR_DELIVERY,
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $drivers = User::where('role', 'driver')->where('is_online', true)->get();

        return view('backend.orders.active', compact('activeOrders', 'drivers'));
    }

    public function reassignDriver(Request $request, $id)
    {
        $request->validate(['driver_id' => 'required|exists:users,id']);

        $order = Order::findOrFail($id);
        $newDriver = User::findOrFail($request->driver_id);

        $order->update([
            'driver_id' => $newDriver->id,
            'driver_accepted_at' => now(),
        ]);

        AuditLog::log(Auth::id(), 'reassign_driver', 'Order', $order->id, "Reassigned to driver {$newDriver->name}");

        InAppNotification::createAlert(
            $newDriver->id,
            $order->id,
            'order_assigned',
            'New Delivery Reassigned to You',
            "Admin assigned Order #{$order->id} to you.",
            'driver',
            'bi-truck'
        );

        InAppNotification::createAlert(
            $order->user_id,
            $order->id,
            'driver_reassigned',
            'Courier Assigned',
            "Your courier is now {$newDriver->name}.",
            'customer',
            'bi-person-badge'
        );

        return redirect()->back()->with('success', "Order #{$order->id} reassigned to {$newDriver->name}.");
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $order = Order::findOrFail($id);
        $order->update([
            'status' => Order::STATUS_REJECTED,
            'delivery_status' => Order::DELIVERY_CANCELLED,
            'cancellation_reason' => $request->reason,
        ]);

        AuditLog::log(Auth::id(), 'cancel_order', 'Order', $order->id, "Cancelled with reason: {$request->reason}");

        InAppNotification::createAlert(
            $order->user_id,
            $order->id,
            'order_cancelled',
            "Order #{$order->id} Cancelled",
            "Reason: {$request->reason}",
            'customer',
            'bi-x-octagon-fill'
        );

        if ($order->driver_id) {
            InAppNotification::createAlert(
                $order->driver_id,
                $order->id,
                'delivery_cancelled',
                "Order #{$order->id} Cancelled by Admin",
                "Reason: {$request->reason}",
                'driver',
                'bi-x-octagon-fill'
            );
        }

        return redirect()->back()->with('success', "Order #{$order->id} has been cancelled.");
    }

    /**
     * Broadcast Notification Tool
     */
    public function broadcastForm()
    {
        return view('backend.broadcast.index');
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'target_role' => 'required|in:all,customer,driver,admin',
        ]);

        InAppNotification::create([
            'user_id' => null, // Broadcast to all of this role
            'order_id' => null,
            'type' => 'broadcast',
            'title' => $request->title,
            'message' => $request->message,
            'target_role' => $request->target_role,
            'icon' => 'bi-megaphone-fill',
            'is_read' => false,
        ]);

        AuditLog::log(Auth::id(), 'broadcast_notification', null, null, "Sent broadcast '{$request->title}' to {$request->target_role}");

        return redirect()->back()->with('success', 'Broadcast announcement published successfully.');
    }

    /**
     * Promo Code Management
     */
    public function promos()
    {
        $promos = PromoCode::orderBy('created_at', 'desc')->paginate(15);

        return view('backend.promos.index', compact('promos'));
    }

    public function storePromo(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:promo_codes,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0.1',
            'min_order' => 'nullable|numeric|min:0',
            'expires_at' => 'nullable|date',
        ]);

        $promo = PromoCode::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order' => $request->min_order ?: 0,
            'is_active' => true,
            'expires_at' => $request->expires_at,
        ]);

        AuditLog::log(Auth::id(), 'created_promo', 'PromoCode', $promo->id, "Code {$promo->code} created");

        return redirect()->back()->with('success', "Promo code '{$promo->code}' created successfully.");
    }

    public function togglePromo($id)
    {
        $promo = PromoCode::findOrFail($id);
        $promo->update(['is_active' => ! $promo->is_active]);

        return redirect()->back()->with('success', "Promo code '{$promo->code}' status updated.");
    }

    /**
     * Disputes & Complaints Management
     */
    public function disputes()
    {
        $disputes = Dispute::with(['order.driver', 'user'])->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.disputes.index', compact('disputes'));
    }

    /**
     * Analytics & Reporting
     */
    public function analytics()
    {
        $totalRevenue = Order::whereIn('status', [1, 2])->sum('amount');
        $totalOrders = Order::count();
        $codOrders = Order::where('payment_method', 'cod')->count();
        $cardOrders = Order::where('payment_method', 'card')->count();

        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.amount * order_items.quantity) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // Driver Performance
        $driverStats = User::where('role', 'driver')
            ->withCount(['deliveries as completed_deliveries' => function ($q) {
                $q->where('delivery_status', Order::DELIVERY_DELIVERED);
            }])
            ->get();

        return view('backend.analytics.index', compact(
            'totalRevenue',
            'totalOrders',
            'codOrders',
            'cardOrders',
            'topProducts',
            'driverStats'
        ));
    }

    /**
     * Audit Log
     */
    public function auditLogs()
    {
        $logs = AuditLog::with('admin')->orderBy('created_at', 'desc')->paginate(20);

        return view('backend.audit.index', compact('logs'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Dispute;
use App\Models\InAppNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $order = Order::where('id', $orderId)->where('user_id', $user->id)->firstOrFail();

        $dispute = Dispute::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'open',
        ]);

        // Alert Admins
        InAppNotification::createAlert(
            null,
            $order->id,
            'dispute_opened',
            "New Dispute Filed for Order #{$order->id}",
            "{$user->name}: {$request->subject}",
            'admin',
            'bi-exclamation-octagon-fill'
        );

        return redirect()->back()->with('success', 'Your complaint has been submitted. Our support team will review it shortly.');
    }

    public function resolve(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:investigating,resolved',
            'resolution_notes' => 'required|string|max:1000',
        ]);

        $dispute = Dispute::with(['order', 'user'])->findOrFail($id);
        $dispute->update([
            'status' => $request->status,
            'resolution_notes' => $request->resolution_notes,
        ]);

        // Log audit
        AuditLog::log(Auth::id(), 'resolved_dispute', 'Dispute', $dispute->id, "Status set to {$request->status}. Notes: {$request->resolution_notes}");

        // Notify customer
        InAppNotification::createAlert(
            $dispute->user_id,
            $dispute->order_id,
            'dispute_update',
            "Update on Dispute for Order #{$dispute->order_id}",
            'Status: '.ucfirst($request->status)." — {$request->resolution_notes}",
            'customer',
            'bi-shield-check'
        );

        return redirect()->back()->with('success', 'Dispute status updated successfully.');
    }
}

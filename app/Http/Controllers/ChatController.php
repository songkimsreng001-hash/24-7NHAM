<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\InAppNotification;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Get messages for an order (between customer and driver or support)
     */
    public function getOrderMessages($orderId)
    {
        $user = Auth::user();
        $order = Order::findOrFail($orderId);

        // Security: user must be either customer, driver, or admin
        if ($order->user_id !== $user->id && $order->driver_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = ChatMessage::with(['sender:id,name,role'])
            ->where('order_id', $order->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark incoming messages as read
        ChatMessage::where('order_id', $order->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message on an order channel
     */
    public function sendOrderMessage(Request $request, $orderId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $order = Order::findOrFail($orderId);

        if ($order->user_id !== $user->id && $order->driver_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Determine recipient
        $recipientId = null;
        $channel = 'driver_customer';

        if ($user->id === $order->user_id) {
            $recipientId = $order->driver_id;
        } elseif ($user->id === $order->driver_id) {
            $recipientId = $order->user_id;
        } elseif ($user->role === 'admin') {
            $recipientId = $order->user_id;
            $channel = 'admin_customer';
        }

        $chatMessage = ChatMessage::create([
            'order_id' => $order->id,
            'sender_id' => $user->id,
            'recipient_id' => $recipientId,
            'channel' => $channel,
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Trigger in-app notification to recipient if exists
        if ($recipientId) {
            InAppNotification::createAlert(
                $recipientId,
                $order->id,
                'chat_message',
                "New message on Order #{$order->id}",
                "{$user->name}: ".Str::limit($request->message, 60),
                $user->isDriver() ? 'customer' : 'driver',
                'bi-chat-dots-fill'
            );
        }

        return response()->json([
            'success' => true,
            'message' => $chatMessage->load('sender:id,name,role'),
        ]);
    }

    /**
     * General Support Chat View & API
     */
    public function supportChatView()
    {
        $user = Auth::user();

        return view('frontend.support.chat', compact('user'));
    }

    public function getSupportMessages()
    {
        $user = Auth::user();

        $messages = ChatMessage::with(['sender:id,name,role'])
            ->where(function ($q) use ($user) {
                $q->where('channel', 'admin_customer')
                    ->where(function ($sub) use ($user) {
                        $sub->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function sendSupportMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $user = Auth::user();

        $msg = ChatMessage::create([
            'order_id' => null,
            'sender_id' => $user->id,
            'recipient_id' => null, // Broadcast to admins
            'channel' => 'admin_customer',
            'message' => $request->message,
            'is_read' => false,
        ]);

        // Notify Admins
        InAppNotification::createAlert(
            null,
            null,
            'support_message',
            "Support request from {$user->name}",
            Str::limit($request->message, 80),
            'admin',
            'bi-headset'
        );

        return response()->json(['success' => true, 'message' => $msg->load('sender:id,name,role')]);
    }
}

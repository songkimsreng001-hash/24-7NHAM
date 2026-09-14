<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
    use HasFactory;

    protected $table = 'in_app_notifications';

    protected $fillable = [
        'user_id',
        'order_id',
        'type',
        'title',
        'message',
        'icon',
        'target_role', // customer, driver, admin, all
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function createAlert(?int $userId, ?int $orderId, string $type, string $title, string $message, string $targetRole = 'customer', string $icon = 'bi-bell'): self
    {
        return self::create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'target_role' => $targetRole,
            'is_read' => false,
        ]);
    }
}

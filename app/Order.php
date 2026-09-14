<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'amount',
        'status',            // FIX: was missing from fillable — caused silent failures
        'payment_intent_id', // NEW: links Stripe transaction to order
    ];

    // Status constants
    const STATUS_REJECTED = 0;

    const STATUS_APPROVED = 1;

    const STATUS_PAID = 2;

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PAID => 'Paid',
            default => 'Unknown',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

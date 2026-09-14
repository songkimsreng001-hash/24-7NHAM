<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'amount',
        'status',               // 0 = rejected, 1 = approved, 2 = paid/pending
        'payment_intent_id',    // Stripe transaction reference
        'payment_method',       // card, cod
        'discount_amount',      // Discount value from promo code
        'promo_code',           // Code applied (if any)
        'tracking_number',      // Unique customer tracking code
        'delivery_status',      // pending, confirmed, preparing, picked_up, out_for_delivery, delivered, cancelled
        'delivery_address',     // Destination address
        'delivery_latitude',
        'delivery_longitude',
        'distance_km',
        'eta_minutes',
        'customer_phone',       // Contact phone for courier
        'delivery_notes',       // Delivery notes or instructions
        'cancellation_reason',  // If cancelled
        'estimated_delivery_at',
        'driver_accepted_at',
        'picked_up_at',
        'delivered_at',
        'driver_id',            // Assigned delivery driver (users.id)
    ];

    protected $casts = [
        'amount' => 'float',
        'discount_amount' => 'float',
        'distance_km' => 'float',
        'eta_minutes' => 'integer',
        'delivery_latitude' => 'float',
        'delivery_longitude' => 'float',
        'estimated_delivery_at' => 'datetime',
        'driver_accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Status constants (Payment / Approval status)
    const STATUS_REJECTED = 0;

    const STATUS_APPROVED = 1;

    const STATUS_PAID = 2;

    // Delivery status constants (5-stage real-time lifecycle)
    const DELIVERY_PENDING = 'pending';

    const DELIVERY_CONFIRMED = 'confirmed';

    const DELIVERY_PREPARING = 'preparing';

    const DELIVERY_PICKED_UP = 'picked_up';

    const DELIVERY_OUT_FOR_DELIVERY = 'out_for_delivery';

    const DELIVERY_DELIVERED = 'delivered';

    const DELIVERY_CANCELLED = 'cancelled';

    // Payment methods
    const PAYMENT_CARD = 'card';

    const PAYMENT_COD = 'cod';

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->tracking_number)) {
                $order->tracking_number = 'TRK-'.date('Ymd').'-'.strtoupper(Str::random(5));
            }
            if (empty($order->delivery_status)) {
                $order->delivery_status = self::DELIVERY_PENDING;
            }
            if (empty($order->payment_method)) {
                $order->payment_method = self::PAYMENT_CARD;
            }
        });
    }

    public function statusLabel(): string
    {
        return match ((int) $this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_PAID => 'Paid',
            default => 'Unknown',
        };
    }

    public function deliveryStatusLabel(): string
    {
        return match ($this->delivery_status) {
            self::DELIVERY_PENDING => 'Placed',
            self::DELIVERY_CONFIRMED => 'Confirmed',
            self::DELIVERY_PREPARING => 'Preparing',
            self::DELIVERY_PICKED_UP => 'Picked Up',
            self::DELIVERY_OUT_FOR_DELIVERY => 'On the Way',
            self::DELIVERY_DELIVERED => 'Delivered',
            self::DELIVERY_CANCELLED => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->delivery_status ?? 'Placed')),
        };
    }

    public function deliveryStatusBadgeClass(): string
    {
        return match ($this->delivery_status) {
            self::DELIVERY_PENDING => 'bg-secondary text-white',
            self::DELIVERY_CONFIRMED => 'bg-info text-dark',
            self::DELIVERY_PREPARING => 'bg-warning text-dark',
            self::DELIVERY_PICKED_UP => 'bg-primary text-white',
            self::DELIVERY_OUT_FOR_DELIVERY => 'bg-primary text-white',
            self::DELIVERY_DELIVERED => 'bg-success text-white',
            self::DELIVERY_CANCELLED => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    public function deliveryStep(): int
    {
        return match ($this->delivery_status) {
            self::DELIVERY_PENDING => 1,
            self::DELIVERY_CONFIRMED,
            self::DELIVERY_PREPARING => 2,
            self::DELIVERY_PICKED_UP => 3,
            self::DELIVERY_OUT_FOR_DELIVERY => 4,
            self::DELIVERY_DELIVERED => 5,
            default => 0, // cancelled
        };
    }

    public function isDelivered(): bool
    {
        return $this->delivery_status === self::DELIVERY_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->delivery_status === self::DELIVERY_CANCELLED;
    }

    public function isActive(): bool
    {
        return in_array($this->delivery_status, [
            self::DELIVERY_PENDING,
            self::DELIVERY_CONFIRMED,
            self::DELIVERY_PREPARING,
            self::DELIVERY_PICKED_UP,
            self::DELIVERY_OUT_FOR_DELIVERY,
        ], true);
    }

    public function isCod(): bool
    {
        return $this->payment_method === self::PAYMENT_COD;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->delivery_status, [
            self::DELIVERY_PENDING,
            self::DELIVERY_CONFIRMED,
        ], true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function review()
    {
        return $this->hasOne(OrderReview::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function dispute()
    {
        return $this->hasOne(Dispute::class);
    }
}

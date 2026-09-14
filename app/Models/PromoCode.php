<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $table = 'promo_codes';

    protected $fillable = [
        'code',
        'discount_type', // percent, fixed
        'discount_value',
        'min_order',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'min_order' => 'float',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function isValid(float $orderAmount): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($orderAmount < $this->min_order) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if (! $this->isValid($orderAmount)) {
            return 0.0;
        }

        if ($this->discount_type === 'percent') {
            return round(($orderAmount * $this->discount_value) / 100, 2);
        }

        return min($orderAmount, (float) $this->discount_value);
    }
}

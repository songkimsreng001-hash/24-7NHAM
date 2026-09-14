<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'phone',
        'status',             // active, suspended, pending_approval
        'vehicle_type',       // motorbike, bicycle, car
        'license_plate',
        'driver_verified_at',
        'is_online',
        'avatar',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'driver_verified_at' => 'datetime',
            'is_online' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCustomer(): bool
    {
        return in_array($this->role, ['customer', 'user', null], true);
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isOnline(): bool
    {
        return (bool) $this->is_online;
    }

    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function driverReviews()
    {
        return $this->hasMany(OrderReview::class, 'driver_id');
    }

    public function averageDriverRating(): float
    {
        return (float) ($this->driverReviews()->avg('driver_rating') ?: 5.0);
    }

    public function completedDeliveriesCount(): int
    {
        return $this->deliveries()->where('delivery_status', Order::DELIVERY_DELIVERED)->count();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function inAppNotifications()
    {
        return $this->hasMany(InAppNotification::class);
    }

    public function unreadNotificationsCount(): int
    {
        return InAppNotification::where(function ($q) {
            $q->where('user_id', $this->id)
                ->orWhere('target_role', $this->role)
                ->orWhere('target_role', 'all');
        })->where('is_read', false)->count();
    }

    /**
     * Get user avatar URL or a handsome fallback avatar
     */
    public function getAvatarUrlAttribute(): string
    {
        if (! empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            if (file_exists(public_path('img/avatars/'.$this->avatar))) {
                return asset('img/avatars/'.$this->avatar);
            }
            if (file_exists(public_path('img/'.$this->avatar))) {
                return asset('img/'.$this->avatar);
            }
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }

            return asset('img/avatars/'.$this->avatar);
        }

        $name = urlencode($this->name ?: 'User');

        return "https://ui-avatars.com/api/?name={$name}&background=16a34a&color=ffffff&size=256&bold=true";
    }
}

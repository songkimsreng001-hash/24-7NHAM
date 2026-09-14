<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'address_line',
        'latitude',
        'longitude',
        'notes',
        'is_default',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate distance from Store Hub (e.g., Central Phnom Penh: 11.5564, 104.9282)
     */
    public function getDistanceFromStore(float $storeLat = 11.5564, float $storeLng = 104.9282): float
    {
        if (empty($this->latitude) || empty($this->longitude)) {
            return 2.5; // fallback default km
        }

        $earthRadius = 6371; // km
        $dLat = deg2rad($this->latitude - $storeLat);
        $dLon = deg2rad($this->longitude - $storeLng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($storeLat)) * cos(deg2rad($this->latitude)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }

    public function getEtaMinutes(): int
    {
        $km = $this->getDistanceFromStore();

        // 15 minutes preparation + ~3.5 min per km
        return (int) max(20, min(75, round(15 + ($km * 3.5))));
    }
}

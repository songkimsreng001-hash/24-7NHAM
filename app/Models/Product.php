<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name', 'price', 'description', 'image', 'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('img/logo.png');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        if (str_starts_with($this->image, 'img/')) {
            return asset($this->image);
        }
        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }
        if (file_exists(public_path('img/'.$this->image))) {
            return asset('img/'.$this->image);
        }
        if (file_exists(public_path('storage/'.$this->image))) {
            return asset('storage/'.$this->image);
        }

        return asset('img/'.$this->image);
    }
}

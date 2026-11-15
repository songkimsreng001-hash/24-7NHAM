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

        // Cache the set of favorited product IDs on the User instance for this request lifecycle
        // This eliminates up to 176 sequential N+1 database queries on product listing pages
        if (! isset($user->_favorited_product_ids)) {
            $user->_favorited_product_ids = \App\Models\Favorite::where('user_id', $user->id)
                ->pluck('product_id')
                ->flip()
                ->toArray();
        }

        return isset($user->_favorited_product_ids[$this->id]);
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('img/logo.png');
        }
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        if (str_starts_with($this->image, 'data:image')) {
            return $this->id ? url('/product-image/'.$this->id) : $this->image;
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

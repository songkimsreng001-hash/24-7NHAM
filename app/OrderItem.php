<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * FIX: OrderItem was defined inside Order.php as a second class.
 * PHP/Laravel requires each model to be in its own file.
 * Moved here and kept the same relationships.
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'amount',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

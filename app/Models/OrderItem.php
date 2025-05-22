<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the digital access for this order item
     */
    public function digitalAccess()
    {
        return $this->hasOne(DigitalAccess::class);
    }
    
    /**
     * Check if this order item is a digital product
     */
    public function isDigitalProduct()
    {
        return $this->product && $this->product->is_digital;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($orderItem) {
            $orderItem->subtotal = $orderItem->quantity * $orderItem->price;
        });
    }
}

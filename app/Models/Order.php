<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REVIEW = 'review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_id',
        'coupon_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'discount',
        'admin_fee',
        'total_amount',
        'status',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_after_discount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the documents for the order.
     */
    public function documents()
    {
        return $this->hasMany(OrderDocument::class);
    }

    /**
     * Generate a unique order number
     */
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        
        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $date . $newNumber;
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_REVIEW => 'Review',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_PROCESSING => 'blue',
            self::STATUS_REVIEW => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray'
        };
    }

    /**
     * Calculate total from items
     */
    public function calculateTotal()
    {
        $this->subtotal = $this->items->sum('subtotal');
        
        // Calculate discount if coupon is applied
        if ($this->coupon_id && $this->coupon) {
            $this->discount_amount = $this->coupon->calculateDiscount($this->subtotal);
        } else {
            $this->discount_amount = 0;
        }
        
        $this->total_after_discount = $this->subtotal - $this->discount_amount;
        $this->total_amount = $this->total_after_discount + $this->admin_fee;
        
        return $this;
    }

    /**
     * Apply coupon to the order
     */
    public function applyCoupon(Coupon $coupon)
    {
        if (!$coupon->isValid($this->subtotal)) {
            return false;
        }

        $this->coupon_id = $coupon->id;
        $this->coupon_code = $coupon->code;
        $this->discount_amount = $coupon->calculateDiscount($this->subtotal);
        $this->total_after_discount = $this->subtotal - $this->discount_amount;
        $this->total_amount = $this->total_after_discount + $this->admin_fee;
        
        $coupon->incrementUsage();
        
        return true;
    }

    /**
     * Remove coupon from the order
     */
    public function removeCoupon()
    {
        $this->coupon_id = null;
        $this->coupon_code = null;
        $this->discount_amount = 0;
        $this->total_after_discount = $this->subtotal;
        $this->total_amount = $this->total_after_discount + $this->admin_fee;
        
        return $this;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

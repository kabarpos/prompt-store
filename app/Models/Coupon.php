<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'is_active',
        'max_uses',
        'used_count',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Define relationship with orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid($subtotal = 0)
    {
        // Cek apakah kupon aktif
        if (!$this->is_active) {
            return false;
        }

        // Cek apakah kupon masih berlaku (periode waktu)
        $now = Carbon::now();
        if ($this->starts_at && $now->isBefore(Carbon::parse($this->starts_at))) {
            return false;
        }
        if ($this->expires_at && $now->isAfter(Carbon::parse($this->expires_at))) {
            return false;
        }

        // Cek apakah kupon masih bisa digunakan (jumlah maksimum penggunaan)
        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) {
            return false;
        }

        // Cek minimum pembelian
        if ($subtotal < $this->min_purchase) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid($subtotal)) {
            return 0;
        }

        $discount = 0;
        
        if ($this->type === 'fixed') {
            $discount = $this->value;
        } else { // percentage
            $discount = $subtotal * ($this->value / 100);
        }
        
        // Apply maximum discount if set
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }
        
        return $discount;
    }

    /**
     * Increment used count
     */
    public function incrementUsage()
    {
        $this->increment('used_count');
        return $this;
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }

        $now = Carbon::now();
        if ($this->starts_at && $now->isBefore(Carbon::parse($this->starts_at))) {
            return 'Belum Berlaku';
        }
        if ($this->expires_at && $now->isAfter(Carbon::parse($this->expires_at))) {
            return 'Kadaluarsa';
        }
        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) {
            return 'Habis';
        }

        return 'Aktif';
    }

    /**
     * Get discount type text
     */
    public function getDiscountTypeTextAttribute()
    {
        return $this->type === 'fixed' ? 'Nominal Tetap' : 'Persentase';
    }

    /**
     * Get formatted discount value
     */
    public function getFormattedValueAttribute()
    {
        if ($this->type === 'fixed') {
            return 'Rp ' . number_format($this->value, 0, ',', '.');
        } else {
            return $this->value . '%';
        }
    }
}

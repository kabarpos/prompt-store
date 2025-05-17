<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'transaction_id',
        'amount',
        'status',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the confirmation for this payment
     */
    public function confirmation()
    {
        return $this->hasOne(PaymentConfirmation::class);
    }

    /**
     * Get all confirmations for this payment
     */
    public function confirmations()
    {
        return $this->hasMany(PaymentConfirmation::class);
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_COMPLETED => 'Pembayaran Diterima',
            self::STATUS_FAILED => 'Pembayaran Gagal',
            default => ucfirst($this->status)
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Type constants
     */
    const TYPE_BANK_TRANSFER = 'bank_transfer';
    const TYPE_PAYMENT_GATEWAY = 'payment_gateway';

    protected $fillable = [
        'name',
        'code',
        'type',
        'account_number',
        'account_name',
        'bank_name',
        'logo',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get payments associated with this payment method
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

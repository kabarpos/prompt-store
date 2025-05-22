<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DigitalAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'order_item_id',
        'product_id',
        'access_code',
        'expires_at',
        'download_count',
        'max_downloads',
        'last_accessed_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_active' => 'boolean',
        'download_count' => 'integer',
        'max_downloads' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($digitalAccess) {
            if (empty($digitalAccess->access_code)) {
                $digitalAccess->access_code = self::generateAccessCode();
            }
        });
    }

    /**
     * Generate unique access code
     */
    public static function generateAccessCode()
    {
        do {
            $code = Str::upper(Str::random(10));
            $exists = self::where('access_code', $code)->exists();
        } while ($exists);
        
        return $code;
    }

    /**
     * Increment download count and update last_accessed_at
     */
    public function recordDownload()
    {
        $this->download_count += 1;
        $this->last_accessed_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Check if access has expired
     */
    public function hasExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }

    /**
     * Check if download limit has been reached
     */
    public function hasReachedDownloadLimit()
    {
        if (!$this->max_downloads) {
            return false;
        }
        
        return $this->download_count >= $this->max_downloads;
    }

    /**
     * Check if access is valid
     */
    public function isAccessible()
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->hasExpired()) {
            return false;
        }
        
        if ($this->hasReachedDownloadLimit()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get days remaining until expiry
     */
    public function daysRemaining()
    {
        if (!$this->expires_at) {
            return null;
        }
        
        $days = now()->diffInDays($this->expires_at, false);
        return max(0, $days);
    }

    /**
     * Get the user that owns this digital access
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that owns this digital access
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the order item that owns this digital access
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product that this digital access belongs to
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
} 
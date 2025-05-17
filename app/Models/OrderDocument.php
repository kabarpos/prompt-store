<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderDocument extends Model
{
    use HasFactory;

    /**
     * Tipe dokumen
     */
    const TYPE_CREDENTIAL = 'credential';
    const TYPE_DOMAIN = 'domain';
    const TYPE_UPDATE = 'update';
    const TYPE_DOWNLOAD = 'download';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'type',
        'title',
        'content',
        'file_path',
        'expires_at',
        'is_sent',
        'sent_at',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the order that owns the document.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    
    /**
     * Get label for document type
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CREDENTIAL => 'Kredensial Login',
            self::TYPE_DOMAIN => 'Informasi Domain',
            self::TYPE_UPDATE => 'Pembaruan',
            self::TYPE_DOWNLOAD => 'File Unduhan',
            default => ucfirst($this->type),
        };
    }
    
    /**
     * Get color class for document type
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CREDENTIAL => 'blue',
            self::TYPE_DOMAIN => 'green',
            self::TYPE_UPDATE => 'orange',
            self::TYPE_DOWNLOAD => 'purple',
            default => 'gray',
        };
    }
    
    /**
     * Get icon for document type
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CREDENTIAL => 'Key',
            self::TYPE_DOMAIN => 'Globe',
            self::TYPE_UPDATE => 'RefreshCw',
            self::TYPE_DOWNLOAD => 'Download',
            default => 'File',
        };
    }
    
    /**
     * Check if document is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }
    
    /**
     * Get expiry status text
     */
    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expires_at) {
            return 'Tidak ada tanggal kedaluwarsa';
        }
        
        if ($this->is_expired) {
            return 'Kedaluwarsa pada ' . $this->expires_at->format('d M Y');
        }
        
        $daysLeft = now()->diffInDays($this->expires_at, false);
        
        if ($daysLeft <= 0) {
            return 'Kedaluwarsa hari ini';
        } elseif ($daysLeft <= 30) {
            return "Kedaluwarsa dalam {$daysLeft} hari";
        } else {
            return 'Kedaluwarsa pada ' . $this->expires_at->format('d M Y');
        }
    }
    
    /**
     * Get file size in bytes
     */
    public function getFileSizeAttribute(): ?int
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return null;
        }
        
        return Storage::size($this->file_path);
    }

    /**
     * Mark document as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Get document read status text
     */
    public function getReadStatusAttribute(): string
    {
        if ($this->is_read) {
            return 'Telah dibaca pada ' . $this->read_at->format('d M Y H:i');
        }
        
        return 'Belum dibaca';
    }
} 
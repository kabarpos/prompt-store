<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminWhatsapp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone_number',
        'description',
        'is_active',
        'order',
        'last_used_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'last_used_at' => 'datetime'
    ];

    /**
     * Format nomor WhatsApp untuk URL
     *
     * @return string
     */
    public function getWhatsappUrl(): string
    {
        // Hapus karakter non-numerik
        $number = preg_replace('/[^0-9]/', '', $this->phone_number);
        
        // Pastikan nomor dalam format internasional
        return 'https://wa.me/' . $number;
    }

    /**
     * Scope untuk hanya mengambil data aktif
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan berdasarkan field order
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get the next available WhatsApp number in rotation
     */
    public static function getNextInRotation()
    {
        try {
            // Log semua data WhatsApp yang aktif
            $allActive = self::where('is_active', true)
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->get();
                
            Log::info('All active WhatsApp numbers:', [
                'count' => $allActive->count(),
                'data' => $allActive->toArray()
            ]);

            if ($allActive->isEmpty()) {
                Log::warning('No active WhatsApp numbers found');
                return null;
            }

            // Coba dapatkan nomor yang belum pernah digunakan
            $unused = self::where('is_active', true)
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->whereNull('last_used_at')
                ->orderBy('order', 'asc')
                ->first();

            if ($unused) {
                Log::info('Found unused WhatsApp number:', ['data' => $unused->toArray()]);
                return $unused;
            }

            // Jika semua sudah digunakan, ambil yang paling lama tidak digunakan
            $next = self::where('is_active', true)
                ->whereNotNull('phone_number')
                ->where('phone_number', '!=', '')
                ->orderBy('last_used_at', 'asc')
                ->orderBy('order', 'asc')
                ->first();

            Log::info('Next WhatsApp number in rotation:', [
                'found' => $next ? true : false,
                'data' => $next ? $next->toArray() : null
            ]);

            return $next;
        } catch (Exception $e) {
            Log::error('Error in getNextInRotation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update last used timestamp
     */
    public function markAsUsed()
    {
        try {
            $this->update(['last_used_at' => now()]);
            Log::info('Marked WhatsApp number as used:', [
                'id' => $this->id,
                'phone_number' => $this->phone_number,
                'last_used_at' => now()
            ]);
        } catch (Exception $e) {
            Log::error('Error in markAsUsed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}

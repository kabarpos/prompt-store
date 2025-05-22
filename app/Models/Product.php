<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasLazyImage;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasLazyImage;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'price',
        'description',
        'featured_image',
        'custom_url',
        'is_active',
        'product_code',
        'product_features',
        'product_values',
        'demo_url',
        'is_digital',
        'has_hidden_content',
        'hidden_content',
        'digital_file_path',
        'download_limit',
        'access_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'product_features' => 'array',
        'product_values' => 'array',
        'is_digital' => 'boolean',
        'has_hidden_content' => 'boolean',
        'hidden_content' => 'array',
        'download_limit' => 'integer',
        'access_days' => 'integer',
    ];

    protected $appends = ['url', 'lazy_image', 'featured_image_url'];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            // Generate slug jika kosong
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            
            // Generate kode produk jika kosong
            if (empty($product->product_code)) {
                $product->product_code = self::generateProductCode();
            }
        });

        static::updating(function ($product) {
            // Jika nama berubah, update slug kecuali custom_url diisi
            if ($product->isDirty('name') && empty($product->custom_url)) {
                $product->slug = Str::slug($product->name);
            }
            
            // Jika custom_url diisi, gunakan itu sebagai slug
            if ($product->isDirty('custom_url') && !empty($product->custom_url)) {
                $product->slug = Str::slug($product->custom_url);
            }
        });

        static::saved(function ($product) {
            self::clearProductCache();
        });

        static::deleted(function ($product) {
            self::clearProductCache();
        });
    }

    /**
     * Hapus cache produk
     */
    public static function clearProductCache()
    {
        // Hapus cache active_products
        Cache::forget('active_products');
        
        // Hapus cache produk individual berdasarkan slug
        $products = self::all();
        foreach ($products as $product) {
            Cache::forget("product_{$product->slug}");
        }
        
        // Hapus cache halaman produk jika menggunakan middleware http-cache
        if (class_exists('App\Services\CacheManager')) {
            app('App\Services\CacheManager')->forgetPattern('page_cache.GET.*products*');
        }
    }

    /**
     * Generate a unique product code with format DLXXX
     */
    public static function generateProductCode()
    {
        // Buat kode random dengan format DLXXX (XXX = kombinasi huruf dan angka)
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 3;
        
        do {
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, strlen($characters) - 1)];
            }
            
            $productCode = 'DL' . $randomString;
            
            // Cek keunikan kode produk
            $exists = self::withTrashed()->where('product_code', $productCode)->exists();
            
        } while ($exists); // Ulangi jika kode sudah digunakan
        
        return $productCode;
    }

    /**
     * Get the URL to the product page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('products.show', $this->slug);
    }

    /**
     * Check if product access has expired for an order
     *
     * @param \App\Models\Order $order
     * @return bool
     */
    public function hasAccessExpired($order)
    {
        if (!$this->is_digital || !$this->access_days) {
            return false; // Tidak ada batasan waktu untuk akses non-digital
        }

        // Hanya hitung dari tanggal order selesai (completed)
        if ($order->status !== Order::STATUS_COMPLETED) {
            return true; // Belum selesai = belum bisa akses
        }

        // Ambil tanggal order status berubah jadi completed
        $completedDate = $order->updated_at;
        
        // Hitung tanggal berakhir akses
        $expiryDate = $completedDate->addDays($this->access_days);
        
        // Bandingkan dengan tanggal sekarang
        return now()->greaterThan($expiryDate);
    }

    /**
     * Get remaining days of access for this product
     *
     * @param \App\Models\Order $order
     * @return int|null
     */
    public function getRemainingAccessDays($order)
    {
        if (!$this->is_digital || !$this->access_days || $order->status !== Order::STATUS_COMPLETED) {
            return null;
        }

        $completedDate = $order->updated_at;
        $expiryDate = $completedDate->addDays($this->access_days);
        
        $remainingDays = now()->diffInDays($expiryDate, false);
        return $remainingDays > 0 ? $remainingDays : 0;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function gallery()
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the digital accesses for this product
     */
    public function digitalAccesses()
    {
        return $this->hasMany(DigitalAccess::class);
    }

    // Cache methods
    public static function getActiveProducts()
    {
        return Cache::remember('active_products', 3600, function () {
            return static::with(['category', 'gallery'])
                ->where('is_active', true)
                ->orderBy('price')
                ->get();
        });
    }

    public static function getProductBySlug($slug)
    {
        return Cache::remember("product_{$slug}", 3600, function () use ($slug) {
            return static::with(['category', 'gallery'])
                ->where('slug', $slug)
                ->firstOrFail();
        });
    }

    /**
     * Get the featured image URL with proper path
     *
     * @return string|null
     */
    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return null;
        }

        // Jika sudah dimulai dengan / atau http, kembalikan apa adanya
        if (str_starts_with($this->featured_image, '/') || str_starts_with($this->featured_image, 'http')) {
            return $this->featured_image;
        }
        
        // Tambahkan /storage/ prefix
        return '/storage/' . $this->featured_image;
    }
}

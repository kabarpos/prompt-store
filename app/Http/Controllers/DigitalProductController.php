<?php

namespace App\Http\Controllers;

use App\Models\DigitalAccess;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DigitalProductController extends Controller
{
    /**
     * Menampilkan semua produk digital milik user
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua akses digital milik user
        $digitalAccesses = DigitalAccess::with(['product', 'order'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Ambil semua order completed yang memiliki produk digital
        $completedOrders = \App\Models\Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->filter(function ($order) {
                return $order->items->contains(function ($item) {
                    return $item->product && $item->product->is_digital;
                });
            });
            
        // Cek jika ada produk digital yang belum dibuat DigitalAccess
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                if ($item->product && $item->product->is_digital) {
                    // Cek apakah sudah ada DigitalAccess untuk item ini
                    $existingAccess = $digitalAccesses->first(function ($access) use ($item) {
                        return $access->order_id == $item->order_id && $access->product_id == $item->product_id;
                    });
                    
                    // Jika belum ada, buat DigitalAccess baru
                    if (!$existingAccess) {
                        $product = $item->product;
                        
                        // Tentukan tanggal kedaluwarsa jika ada
                        $expiresAt = null;
                        if ($product->access_days > 0) {
                            $expiresAt = now()->addDays($product->access_days);
                        }
                        
                        // Buat entri DigitalAccess
                        $digitalAccess = DigitalAccess::create([
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'order_item_id' => $item->id,
                            'product_id' => $product->id,
                            'max_downloads' => $product->download_limit,
                            'expires_at' => $expiresAt,
                            'is_active' => true,
                        ]);
                        
                        // Tambahkan ke koleksi
                        $digitalAccesses->push($digitalAccess);
                    }
                }
            }
        }
            
        $myProducts = $digitalAccesses->map(function($access) {
            $product = $access->product;
            $hasDigitalFile = !empty($product->digital_file_path);
            
            return [
                'id' => $access->id,
                'order_id' => $access->order_id,
                'order_number' => $access->order->order_number,
                'product_id' => $access->product_id,
                'product_name' => $access->product->name,
                'product_image' => $access->product->featured_image_url,
                'product_description' => $access->product->description,
                'access_code' => $access->access_code,
                'is_expired' => $access->hasExpired(),
                'is_active' => $access->is_active,
                'has_reached_limit' => $access->hasReachedDownloadLimit(),
                'is_accessible' => $access->isAccessible(),
                'download_count' => $access->download_count,
                'max_downloads' => $access->max_downloads,
                'days_remaining' => $access->daysRemaining(),
                'expires_at' => $access->expires_at,
                'purchased_at' => $access->created_at,
                'last_accessed' => $access->last_accessed_at,
                'has_digital_file' => $hasDigitalFile,
            ];
        });
        
        return Inertia::render('digital-products/Index', [
            'myProducts' => $myProducts
        ]);
    }
    
    /**
     * Menampilkan detail produk digital dan akses
     */
    public function show($id)
    {
        $user = Auth::user();
        
        // Ambil digital access dan validasi kepemilikan
        $digitalAccess = DigitalAccess::with(['product', 'order'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // Load informasi tambahan yang dibutuhkan
        $product = $digitalAccess->product;
        $order = $digitalAccess->order;
        
        // Periksa apakah produk memiliki file digital
        $hasDigitalFile = !empty($product->digital_file_path);
        
        return Inertia::render('digital-products/Show', [
            'digitalAccess' => [
                'id' => $digitalAccess->id,
                'access_code' => $digitalAccess->access_code,
                'is_expired' => $digitalAccess->hasExpired(),
                'is_active' => $digitalAccess->is_active,
                'has_reached_limit' => $digitalAccess->hasReachedDownloadLimit(),
                'is_accessible' => $digitalAccess->isAccessible(),
                'download_count' => $digitalAccess->download_count,
                'max_downloads' => $digitalAccess->max_downloads,
                'days_remaining' => $digitalAccess->daysRemaining(),
                'expires_at' => $digitalAccess->expires_at,
                'purchased_at' => $digitalAccess->created_at,
                'last_accessed' => $digitalAccess->last_accessed_at,
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'featured_image' => $product->featured_image_url,
                'product_features' => $product->product_features,
                'has_hidden_content' => $product->has_hidden_content,
                'hidden_content' => $product->has_hidden_content ? $product->hidden_content : null,
                'has_digital_file' => $hasDigitalFile,
            ],
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number
            ]
        ]);
    }
    
    /**
     * Download file produk digital
     */
    public function download($id)
    {
        $user = Auth::user();
        
        // Ambil digital access dan validasi
        $digitalAccess = DigitalAccess::with('product')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // Periksa apakah masih bisa diakses
        if (!$digitalAccess->isAccessible()) {
            if ($digitalAccess->hasExpired()) {
                return redirect()->route('digital-products.index')
                    ->with('error', 'Akses ke produk ini telah kedaluwarsa.');
            }
            
            if ($digitalAccess->hasReachedDownloadLimit()) {
                return redirect()->route('digital-products.index')
                    ->with('error', 'Anda telah mencapai batas maksimum unduhan untuk produk ini.');
            }
            
            return redirect()->route('digital-products.index')
                ->with('error', 'Akses ke produk ini tidak aktif.');
        }
        
        // Verifikasi file tersedia
        $product = $digitalAccess->product;
        
        // Normalisasi path file
        $filePath = $product->digital_file_path;
        
        if (empty($filePath)) {
            return redirect()->route('digital-products.index')
                ->with('error', 'File produk tidak ditemukan. Silakan hubungi administrator.');
        }
        
        // Hapus prefix /storage/ jika ada (untuk kompatibilitas dengan format penyimpanan lama)
        if (str_starts_with($filePath, '/storage/')) {
            $filePath = str_replace('/storage/', '', $filePath);
            $disk = 'public';
        } else {
            $disk = 'local'; // Default menggunakan disk local (private)
        }
        
        if (!Storage::disk($disk)->exists($filePath)) {
            // Coba disk alternatif jika file tidak ditemukan
            $alternateDisk = $disk === 'public' ? 'local' : 'public';
            
            if (!Storage::disk($alternateDisk)->exists($filePath)) {
                return redirect()->route('digital-products.index')
                    ->with('error', 'File produk tidak ditemukan di sistem penyimpanan. Silakan hubungi administrator.');
            }
            
            // Gunakan disk alternatif jika file ditemukan
            $disk = $alternateDisk;
        }
        
        // Rekam aktivitas download
        $digitalAccess->recordDownload();
        
        // Unduh file
        $fileName = pathinfo($filePath, PATHINFO_BASENAME);
        return Storage::disk($disk)->download($filePath, $fileName);
    }

    private function getCorrectImagePath($imagePath)
    {
        if ($imagePath && !str_starts_with($imagePath, '/') && !str_starts_with($imagePath, 'http')) {
            return '/storage/' . $imagePath;
        }
        return $imagePath;
    }
} 
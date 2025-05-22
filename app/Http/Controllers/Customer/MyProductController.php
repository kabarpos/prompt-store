<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDocument;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MyProductController extends Controller
{
    /**
     * Menampilkan daftar produk digital yang dimiliki customer
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil semua order dengan status completed milik user
        $completedOrders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->get();
            
        // Kumpulkan semua produk digital dari order completed
        $myProducts = collect();
        
        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                // Hanya ambil produk digital
                if (!$item->product || !$item->product->is_digital) {
                    continue;
                }
                
                $product = $item->product;
                
                // Ambil dokumen unduhan terkait produk ini
                $documents = $order->documents()
                    ->where('type', OrderDocument::TYPE_DOWNLOAD)
                    ->get();
                
                // Cek apakah akses sudah kedaluwarsa
                $isExpired = $product->hasAccessExpired($order);
                $remainingDays = $product->getRemainingAccessDays($order);
                
                $myProducts->push([
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'featured_image' => $product->featured_image,
                    'product_code' => $product->product_code,
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'purchased_at' => $order->updated_at,
                    'is_expired' => $isExpired,
                    'remaining_days' => $remainingDays,
                    'documents' => $documents,
                    'has_documents' => $documents->count() > 0
                ]);
            }
        }
        
        return Inertia::render('customer/MyProducts/Index', [
            'myProducts' => $myProducts
        ]);
    }
    
    /**
     * Menampilkan detail produk digital spesifik
     */
    public function show($orderId, $productId)
    {
        $user = Auth::user();
        
        // Verifikasi bahwa user memiliki akses ke produk ini
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->firstOrFail();
            
        $orderItem = OrderItem::where('order_id', $orderId)
            ->where('product_id', $productId)
            ->firstOrFail();
            
        $product = $orderItem->product;
        
        // Verifikasi bahwa ini adalah produk digital
        if (!$product->is_digital) {
            return redirect()->route('dashboard.my-products.index')
                ->with('error', 'Produk ini bukan produk digital.');
        }
        
        // Ambil dokumen terkait produk ini
        $documents = $order->documents()
            ->where('type', OrderDocument::TYPE_DOWNLOAD)
            ->get();
            
        // Cek apakah akses sudah kedaluwarsa
        $isExpired = $product->hasAccessExpired($order);
        $remainingDays = $product->getRemainingAccessDays($order);
        
        return Inertia::render('customer/MyProducts/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'featured_image' => $product->featured_image,
                'product_code' => $product->product_code,
                'product_features' => $product->product_features,
                'product_values' => $product->product_values,
            ],
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'purchased_at' => $order->updated_at,
            ],
            'is_expired' => $isExpired,
            'remaining_days' => $remainingDays,
            'documents' => $documents
        ]);
    }
    
    /**
     * Download file dokumen
     */
    public function downloadDocument($documentId)
    {
        $user = Auth::user();
        
        // Cari dokumen dan verifikasi akses
        $document = OrderDocument::with('order')
            ->findOrFail($documentId);
            
        // Verifikasi bahwa order milik user ini
        if ($document->order->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke dokumen ini.');
        }
        
        // Verifikasi bahwa order sudah completed
        if ($document->order->status !== Order::STATUS_COMPLETED) {
            abort(403, 'Order belum selesai.');
        }
        
        // Verifikasi bahwa dokumen belum kedaluwarsa
        if ($document->expires_at && $document->expires_at->isPast()) {
            abort(403, 'Akses dokumen sudah kedaluwarsa.');
        }
        
        // Dapatkan produk terkait
        $orderItem = OrderItem::where('order_id', $document->order_id)
            ->whereHas('product', function($query) {
                $query->where('is_digital', true);
            })
            ->first();
            
        if (!$orderItem || !$orderItem->product) {
            abort(404, 'Produk digital tidak ditemukan.');
        }
        
        $product = $orderItem->product;
        
        // Verifikasi bahwa akses produk belum kedaluwarsa
        if ($product->hasAccessExpired($document->order)) {
            abort(403, 'Akses produk sudah kedaluwarsa.');
        }
        
        // Tandai dokumen sebagai dibaca
        $document->markAsRead();
        
        // Jika tidak ada file path, kembalikan pesan error
        if (!$document->file_path) {
            abort(404, 'File tidak ditemukan.');
        }
        
        // Return file untuk diunduh
        return response()->download(storage_path('app/' . $document->file_path));
    }
} 
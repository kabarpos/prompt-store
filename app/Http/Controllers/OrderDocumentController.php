<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class OrderDocumentController extends Controller
{
    /**
     * Tampilkan dokumen pesanan ke pelanggan
     */
    public function index(Order $order)
    {
        // Pastikan hanya pemilik pesanan yang bisa mengakses
        $this->authorize('view', $order);
        
        $order->load(['documents' => function($query) {
            $query->latest();
        }]);
        
        // Kelompokkan dokumen berdasarkan jenis
        $credentials = $order->documents->where('type', OrderDocument::TYPE_CREDENTIAL);
        $domainInfo = $order->documents->where('type', OrderDocument::TYPE_DOMAIN);
        $updates = $order->documents->where('type', OrderDocument::TYPE_UPDATE);
        $downloads = $order->documents->where('type', OrderDocument::TYPE_DOWNLOAD);
        
        return Inertia::render('orders/documents/Index', [
            'order' => $order,
            'credentials' => $credentials,
            'domainInfo' => $domainInfo,
            'updates' => $updates,
            'downloads' => $downloads,
        ]);
    }
    
    /**
     * Tampilkan semua dokumen pelanggan dari semua pesanan
     */
    public function allDocuments()
    {
        $user = Auth::user();
        
        // Dapatkan semua order milik user ini
        $orderIds = $user->orders()->pluck('id');
        
        // Jika tidak ada pesanan, kembalikan array kosong
        if ($orderIds->isEmpty()) {
            return Inertia::render('orders/documents/AllDocuments', [
                'documents' => [],
                'breadcrumbs' => [
                    ['title' => 'Dashboard', 'href' => route('dashboard')],
                    ['title' => 'Dokumen Saya', 'href' => route('my-documents')],
                ],
            ]);
        }
        
        // Ambil semua dokumen yang terkait dengan order-order tersebut
        $documents = OrderDocument::whereIn('order_id', $orderIds)
            ->with('order')
            ->latest()
            ->get()
            ->unique(function($item) {
                // Mengelompokkan berdasarkan kombinasi dari judul, order_id, dan tipe
                return $item->title . '-' . $item->order_id . '-' . $item->type;
            });
        
        return Inertia::render('orders/documents/AllDocuments', [
            'documents' => $documents->values(),
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'href' => route('dashboard')],
                ['title' => 'Dokumen Saya', 'href' => route('my-documents')],
            ],
        ]);
    }
    
    /**
     * Tampilkan detail dokumen
     */
    public function show(Order $order, OrderDocument $document)
    {
        // Pastikan hanya pemilik pesanan yang bisa mengakses
        $this->authorize('view', $order);
        $this->authorize('view', $document);
        
        // Tandai dokumen sebagai telah dibaca
        if (!$document->is_read) {
            $document->markAsRead();
        }
        
        return Inertia::render('orders/documents/Show', [
            'order' => $order,
            'document' => $document,
        ]);
    }
    
    /**
     * Tandai dokumen sebagai dibaca
     */
    public function markAsRead(Order $order, OrderDocument $document)
    {
        // Pastikan hanya pemilik pesanan yang bisa mengakses
        $this->authorize('view', $order);
        $this->authorize('view', $document);
        
        $document->markAsRead();
        
        return back()->with('message', 'Dokumen ditandai sebagai telah dibaca');
    }
    
    /**
     * Unduh file dokumen
     */
    public function download(Order $order, OrderDocument $document)
    {
        // Pastikan hanya pemilik pesanan yang bisa mengakses
        $this->authorize('view', $order);
        $this->authorize('view', $document);
        
        // Tandai dokumen sebagai telah dibaca saat diunduh
        if (!$document->is_read) {
            $document->markAsRead();
        }
        
        if (!$document->file_path) {
            return back()->with('error', 'File tidak tersedia untuk diunduh');
        }
        
        return Storage::download($document->file_path, $document->title . ' - ' . $order->order_number . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDocument;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class OrderDocumentController extends Controller
{
    protected $notificationService;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tampilkan daftar dokumen untuk pesanan
     */
    public function index(Order $order)
    {
        // Pastikan data order lengkap dengan user jika ada
        $order->load('user');
        
        $order->load(['documents' => function($query) {
            $query->latest();
        }]);
        
        return Inertia::render('admin/orders/documents/Index', [
            'order' => $order,
            'documentTypes' => [
                OrderDocument::TYPE_CREDENTIAL => 'Kredensial Login',
                OrderDocument::TYPE_DOMAIN => 'Informasi Domain',
                OrderDocument::TYPE_UPDATE => 'Pembaruan',
                OrderDocument::TYPE_DOWNLOAD => 'File Unduhan',
            ],
        ]);
    }
    
    /**
     * Tampilkan form untuk membuat dokumen baru
     */
    public function create($orderParam)
    {
        // Jika parameter order adalah 'new', maka ini adalah halaman create umum
        // yang memungkinkan memilih order
        if ($orderParam === 'new') {
            // Ambil daftar order yang tersedia untuk dropdown
            $availableOrders = Order::select('id', 'order_number', 'customer_name', 'user_id')
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($order) {
                    // Gunakan customer_name langsung dari order jika ada, atau nama user jika ada
                    $customerName = $order->customer_name ?: ($order->user ? $order->user->name : 'Tanpa Nama');
                    
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $customerName,
                    ];
                });
                
            return Inertia::render('admin/orders/documents/Create', [
                'orderParam' => $orderParam,
                'availableOrders' => $availableOrders,
                'documentTypes' => [
                    OrderDocument::TYPE_CREDENTIAL => 'Kredensial Login',
                    OrderDocument::TYPE_DOMAIN => 'Informasi Domain',
                    OrderDocument::TYPE_UPDATE => 'Pembaruan',
                    OrderDocument::TYPE_DOWNLOAD => 'File Unduhan',
                    'other' => 'Lainnya',
                ],
            ]);
        }
        
        // Jika bukan 'new', maka parameter adalah ID order
        $order = Order::findOrFail($orderParam);
        
        return Inertia::render('admin/orders/documents/Create', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name ?: ($order->user ? $order->user->name : 'Tanpa Nama'),
            ],
            'orderParam' => $orderParam,
            'availableOrders' => [
                [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name ?: ($order->user ? $order->user->name : 'Tanpa Nama'),
                ]
            ],
            'documentTypes' => [
                OrderDocument::TYPE_CREDENTIAL => 'Kredensial Login',
                OrderDocument::TYPE_DOMAIN => 'Informasi Domain',
                OrderDocument::TYPE_UPDATE => 'Pembaruan',
                OrderDocument::TYPE_DOWNLOAD => 'File Unduhan',
                'other' => 'Lainnya',
            ],
        ]);
    }
    
    /**
     * Simpan dokumen baru
     */
    public function store(Request $request, $orderId)
    {
        // Pastikan order_id yang valid
        $order = Order::findOrFail($orderId);
        
        $validated = $request->validate([
            'type' => 'required|in:credential,domain,update,download,other',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'expires_at' => 'nullable|date',
            'should_notify' => 'boolean',
            'file' => 'nullable|file|max:10240',
        ]);
        
        $document = $order->documents()->create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('order-documents');
            $document->update(['file_path' => $path]);
        }
        
        // Kirim notifikasi jika diminta
        if ($request->boolean('should_notify', false)) {
            switch ($validated['type']) {
                case OrderDocument::TYPE_CREDENTIAL:
                    $this->notificationService->sendCredentials($document);
                    break;
                case OrderDocument::TYPE_DOMAIN:
                    $this->notificationService->sendDomainInfo($document);
                    break;
                case OrderDocument::TYPE_UPDATE:
                    $this->notificationService->sendUpdateInfo($document);
                    break;
                case OrderDocument::TYPE_DOWNLOAD:
                    $this->notificationService->sendDownloadLink($document);
                    break;
                // case 'other' dihandle secara default
            }
        }
        
        // Periksa jika request dari halaman allDocuments atau tidak
        if ($request->headers->get('referer') && str_contains($request->headers->get('referer'), 'order-documents')) {
            return redirect()->route('admin.documents.all')
                ->with('message', 'Dokumen berhasil dibuat');
        }
        
        return redirect()->route('admin.orders.documents.index', $order)
            ->with('message', 'Dokumen berhasil dibuat');
    }
    
    /**
     * Tampilkan form untuk edit dokumen
     */
    public function edit(Order $order, OrderDocument $document)
    {
        return Inertia::render('admin/orders/documents/Edit', [
            'order' => $order,
            'document' => $document,
            'documentTypes' => [
                OrderDocument::TYPE_CREDENTIAL => 'Kredensial Login',
                OrderDocument::TYPE_DOMAIN => 'Informasi Domain',
                OrderDocument::TYPE_UPDATE => 'Pembaruan',
                OrderDocument::TYPE_DOWNLOAD => 'File Unduhan',
            ],
        ]);
    }
    
    /**
     * Update dokumen
     */
    public function update(Request $request, Order $order, OrderDocument $document)
    {
        $validated = $request->validate([
            'type' => 'required|in:credential,domain,update,download',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'expires_at' => 'nullable|date',
            'should_notify' => 'boolean',
            'file' => 'nullable|file|max:10240',
        ]);
        
        $document->update([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'expires_at' => $validated['expires_at'] ?? null,
        ]);
        
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($document->file_path) {
                Storage::delete($document->file_path);
            }
            
            $path = $request->file('file')->store('order-documents');
            $document->update(['file_path' => $path]);
        }
        
        // Kirim notifikasi jika diminta
        if ($request->boolean('should_notify', false)) {
            switch ($validated['type']) {
                case OrderDocument::TYPE_CREDENTIAL:
                    $this->notificationService->sendCredentials($document);
                    break;
                case OrderDocument::TYPE_DOMAIN:
                    $this->notificationService->sendDomainInfo($document);
                    break;
                case OrderDocument::TYPE_UPDATE:
                    $this->notificationService->sendUpdateInfo($document);
                    break;
                case OrderDocument::TYPE_DOWNLOAD:
                    $this->notificationService->sendDownloadLink($document);
                    break;
            }
        }
        
        return redirect()->route('admin.orders.documents.index', $order)
            ->with('message', 'Dokumen berhasil diperbarui');
    }
    
    /**
     * Hapus dokumen
     */
    public function destroy(Order $order, OrderDocument $document)
    {
        // Hapus file jika ada
        if ($document->file_path) {
            Storage::delete($document->file_path);
        }
        
        // Cek apakah request berasal dari halaman "All Documents" 
        // dengan memeriksa parameter yang dikirim dari frontend
        $fromAllDocuments = request()->boolean('from_all_documents', false);
        
        $document->delete();
        
        // Jika request berasal dari halaman "All Documents", redirect ke halaman tersebut
        if ($fromAllDocuments) {
            return redirect()->route('admin.documents.all')
                ->with('message', 'Dokumen berhasil dihapus');
        }
        
        // Jika tidak, redirect ke halaman dokumen order
        return redirect()->route('admin.orders.documents.index', $order)
            ->with('message', 'Dokumen berhasil dihapus');
    }
    
    /**
     * Kirim dokumen melalui email
     */
    public function send(Order $order, OrderDocument $document)
    {
        $success = false;
        
        switch ($document->type) {
            case OrderDocument::TYPE_CREDENTIAL:
                $success = $this->notificationService->sendCredentials($document);
                break;
            case OrderDocument::TYPE_DOMAIN:
                $success = $this->notificationService->sendDomainInfo($document);
                break;
            case OrderDocument::TYPE_UPDATE:
                $success = $this->notificationService->sendUpdateInfo($document);
                break;
            case OrderDocument::TYPE_DOWNLOAD:
                $success = $this->notificationService->sendDownloadLink($document);
                break;
        }
        
        // Pastikan status dokumen diperbarui secara eksplisit
        if ($success) {
            // Update status dokumen secara manual untuk memastikan perubahan tersimpan
            $document->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
            
            // Refresh dokumen untuk mendapatkan data terbaru setelah update
            $document->refresh();
        }
        
        // Log informasi status dokumen untuk debugging
        \Log::info('Dokumen status:', [
            'document_id' => $document->id,
            'is_sent' => $document->is_sent,
            'sent_at' => $document->sent_at
        ]);
        
        // Untuk Inertia requests, selalu gunakan redirect
        if ($success) {
            return redirect()->back()->with([
                'message' => 'Dokumen berhasil dikirim',
                'success' => true,
                'document' => $document, // Sertakan dokumen yang sudah diupdate
            ]);
        } else {
            return redirect()->back()->with([
                'error' => 'Gagal mengirim dokumen',
                'success' => false,
            ]);
        }
    }

    /**
     * Menampilkan semua dokumen order
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function allDocuments(Request $request)
    {
        $query = OrderDocument::query()
            ->with(['order', 'order.user'])
            ->latest();

        // Filter berdasarkan search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery->where('name', 'like', "%{$search}%");
                                });
                  });
            });
        }

        $perPage = $request->get('per_page', 10);

        $documents = $query->paginate($perPage)->withQueryString();
        
        // Tambahkan append attributes
        $documents->getCollection()->each->append(['type_label', 'type_icon', 'type_color', 'file_size']);

        // Ambil daftar order yang tersedia untuk dropdown
        $availableOrders = Order::select('id', 'order_number', 'customer_name', 'user_id')
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                // Gunakan customer_name langsung jika ada, atau ambil dari user jika ada
                $customerName = $order->customer_name ?: ($order->user ? $order->user->name : 'Tanpa Nama');
                
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $customerName,
                ];
            });

        return Inertia::render('admin/orders/documents/AllDocuments', [
            'documents' => $documents,
            'filters' => [
                'search' => $request->search,
                'per_page' => (int) $perPage,
            ],
            'available_orders' => $availableOrders,
        ]);
    }

    /**
     * Download dokumen
     *
     * @param OrderDocument $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(OrderDocument $document)
    {
        abort_if(!Storage::exists($document->file_path), 404);

        // Buat nama file yang benar untuk diunduh
        $filename = Str::slug(substr($document->title, 0, 100)) . '.' . pathinfo(Storage::path($document->file_path), PATHINFO_EXTENSION);

        return response()->download(
            Storage::path($document->file_path),
            $filename
        );
    }

    /**
     * Menampilkan detail dokumen
     */
    public function show(Order $order, OrderDocument $document)
    {
        // Pastikan data order lengkap dengan user jika ada
        $order->load('user');
        
        return Inertia::render('admin/orders/documents/Show', [
            'order' => $order,
            'document' => $document,
            'documentTypes' => [
                OrderDocument::TYPE_CREDENTIAL => 'Kredensial Login',
                OrderDocument::TYPE_DOMAIN => 'Informasi Domain',
                OrderDocument::TYPE_UPDATE => 'Pembaruan',
                OrderDocument::TYPE_DOWNLOAD => 'File Unduhan',
            ],
        ]);
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        $query = Order::query()
            ->with(['user', 'items.product'])
            ->latest();
            
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by search terms (order number, customer name, customer phone)
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                // Mencari dengan exact match untuk nomor pesanan
                $q->where('order_number', $search)
                  // Mencari dengan partial match jika nomor pesanan tidak ditemukan
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by date range
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->paginate(10)
            ->withQueryString();
            
        return Inertia::render('admin/orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['status', 'search', 'date_from', 'date_to']),
            'statuses' => [
                Order::STATUS_PENDING => 'Menunggu Konfirmasi',
                Order::STATUS_PROCESSING => 'Sedang Diproses',
                Order::STATUS_REVIEW => 'Review',
                Order::STATUS_COMPLETED => 'Selesai',
                Order::STATUS_CANCELLED => 'Dibatalkan',
            ]
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product.category']);
        
        return Inertia::render('admin/orders/Show', [
            'order' => $order,
            'statuses' => [
                Order::STATUS_PENDING => 'Menunggu Konfirmasi',
                Order::STATUS_PROCESSING => 'Sedang Diproses',
                Order::STATUS_REVIEW => 'Review',
                Order::STATUS_COMPLETED => 'Selesai',
                Order::STATUS_CANCELLED => 'Dibatalkan',
            ]
        ]);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', [
                Order::STATUS_PENDING,
                Order::STATUS_PROCESSING,
                Order::STATUS_REVIEW,
                Order::STATUS_COMPLETED,
                Order::STATUS_CANCELLED,
            ]),
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        // Update status
        $oldStatus = $order->status;
        $order->status = $request->status;
        
        if ($request->filled('admin_notes')) {
            $order->admin_notes = $request->admin_notes;
        }
        
        $order->save();

        // Kirim notifikasi WhatsApp
        try {
            app(WhatsAppService::class)->sendOrderStatusChangedNotification($order, $oldStatus);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi WhatsApp perubahan status', ['error' => $e->getMessage()]);
        }

        return back()->with('success', 'Status order berhasil diperbarui');
    }

    /**
     * Export order to PDF.
     */
    public function exportPdf(Order $order)
    {
        // Implementation for generating PDF invoice
        $order->load(['user', 'items.product']);
        
        // Implementasi sederhana, dapat ditingkatkan dengan library PDF seperti DomPDF
        return response('Fitur ekspor PDF belum diimplementasikan sepenuhnya', 501)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show order statistics.
     */
    public function statistics(Request $request)
    {
        $period = $request->input('period', 'month');
        
        // Menentukan range tanggal berdasarkan periode
        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'quarter':
                $startDate = Carbon::now()->subQuarter();
                break;
            case 'year':
                $startDate = Carbon::now()->subYear();
                break;
            default:
                $startDate = Carbon::now()->subMonth();
        }
        
        // Dapatkan data statistik
        $totalOrders = Order::where('created_at', '>=', $startDate)->count();
        $pendingOrders = Order::where('created_at', '>=', $startDate)
            ->where('status', Order::STATUS_PENDING)->count();
        $processingOrders = Order::where('created_at', '>=', $startDate)
            ->where('status', Order::STATUS_PROCESSING)->count();
        $completedOrders = Order::where('created_at', '>=', $startDate)
            ->where('status', Order::STATUS_COMPLETED)->count();
        $cancelledOrders = Order::where('created_at', '>=', $startDate)
            ->where('status', Order::STATUS_CANCELLED)->count();
        
        // Dapatkan pesanan terbaru
        $recentOrders = Order::with('items.product', 'user')
            ->latest()
            ->take(5)
            ->get();
            
        // Hitung total pendapatan
        $totalRevenue = Order::where('created_at', '>=', $startDate)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->sum('total_amount');
            
        // Produk terlaris
        $topProducts = Product::select('products.id', 'products.name', 'products.slug', 'products.price')
            ->selectRaw('SUM(order_items.quantity) as total_ordered')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->groupBy('products.id', 'products.name', 'products.slug', 'products.price')
            ->orderByDesc('total_ordered')
            ->limit(5)
            ->get();
            
        // Pelanggan teratas
        $topCustomers = User::select('users.id', 'users.name', 'users.email')
            ->selectRaw('COUNT(orders.id) as order_count')
            ->selectRaw('SUM(orders.total_amount) as total_spent')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.status', '!=', Order::STATUS_CANCELLED)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
        
        return Inertia::render('admin/orders/Statistics', [
            'statistics' => [
                'totalOrders' => $totalOrders,
                'pendingOrders' => $pendingOrders,
                'processingOrders' => $processingOrders,
                'completedOrders' => $completedOrders,
                'cancelledOrders' => $cancelledOrders,
                'totalRevenue' => $totalRevenue,
                'period' => $period,
            ],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
        ]);
    }
} 
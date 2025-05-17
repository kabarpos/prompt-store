<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard admin
     */
    public function index()
    {
        return Inertia::render('admin/dashboard/Index');
    }

    /**
     * Mengambil data untuk dashboard admin
     */
    public function getData()
    {
        // Data pengguna
        $usersCount = User::count();
        $lastMonthUsersCount = User::where('created_at', '<', now()->subMonth())->count();
        $usersTrend = $lastMonthUsersCount > 0 
            ? round((($usersCount - $lastMonthUsersCount) / $lastMonthUsersCount) * 100) 
            : 0;
        
        // Data order
        $ordersCount = Order::count();
        $lastMonthOrdersCount = Order::where('created_at', '<', now()->subMonth())->count();
        $ordersTrend = $lastMonthOrdersCount > 0 
            ? round((($ordersCount - $lastMonthOrdersCount) / $lastMonthOrdersCount) * 100) 
            : 0;
        
        // Data pendapatan
        $totalIncome = Order::where('status', '!=', Order::STATUS_CANCELLED)->sum('total_amount');
        $lastMonthIncome = Order::where('status', '!=', Order::STATUS_CANCELLED)
            ->where('created_at', '<', now()->subMonth())
            ->sum('total_amount');
        $incomeTrend = $lastMonthIncome > 0 
            ? round((($totalIncome - $lastMonthIncome) / $lastMonthIncome) * 100) 
            : 0;
        
        // Data pengeluaran
        $totalExpenses = Expense::sum('amount');
        $lastMonthExpenses = Expense::where('created_at', '<', now()->subMonth())->sum('amount');
        $expensesTrend = $lastMonthExpenses > 0 
            ? round((($totalExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100) 
            : 0;
        
        // Statistik order berdasarkan status
        $pendingOrders = Order::where('status', Order::STATUS_PENDING)->count();
        $processingOrders = Order::where('status', Order::STATUS_PROCESSING)->count();
        $reviewOrders = Order::where('status', Order::STATUS_REVIEW)->count();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)->count();
        $cancelledOrders = Order::where('status', Order::STATUS_CANCELLED)->count();
        
        // Data chart
        $chartData = [
            [
                'status' => 'pending',
                'label' => 'Menunggu Pembayaran',
                'value' => $pendingOrders,
            ],
            [
                'status' => 'processing',
                'label' => 'Diproses',
                'value' => $processingOrders,
            ],
            [
                'status' => 'review',
                'label' => 'Review',
                'value' => $reviewOrders,
            ],
            [
                'status' => 'completed',
                'label' => 'Selesai',
                'value' => $completedOrders,
            ],
            [
                'status' => 'cancelled',
                'label' => 'Dibatalkan',
                'value' => $cancelledOrders,
            ],
        ];
        
        // Order terbaru
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'invoice' => $order->order_number,
                    'customer' => $order->user ? $order->user->name : $order->customer_name,
                    'amount' => $order->total_amount,
                    'status' => $order->status,
                    'created_at' => $order->created_at,
                ];
            });
        
        // Produk terlaris
        $topProducts = Product::withCount(['orderItems as sold' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', '!=', Order::STATUS_CANCELLED);
                });
            }])
            ->orderBy('sold', 'desc')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sold' => $product->sold,
                    'price' => $product->price,
                ];
            });
        
        // Aktivitas terbaru
        $activities = collect([
            [
                'id' => 1,
                'user' => 'Admin',
                'type' => 'user',
                'action' => 'menambahkan pengguna baru',
                'created_at' => now()->subHours(2),
            ],
            [
                'id' => 2,
                'user' => 'Admin',
                'type' => 'order',
                'action' => 'mengubah status pesanan #ORD123456',
                'created_at' => now()->subHours(3),
            ],
            [
                'id' => 3,
                'user' => 'Admin',
                'type' => 'product',
                'action' => 'menambahkan produk baru',
                'created_at' => now()->subHours(5),
            ],
        ]);
        
        return response()->json([
            'usersCount' => $usersCount,
            'usersTrend' => $usersTrend,
            'ordersCount' => $ordersCount,
            'ordersTrend' => $ordersTrend,
            'totalIncome' => $totalIncome,
            'incomeTrend' => $incomeTrend,
            'totalExpenses' => $totalExpenses,
            'expensesTrend' => $expensesTrend,
            'pendingOrders' => $pendingOrders,
            'processingOrders' => $processingOrders,
            'reviewOrders' => $reviewOrders,
            'completedOrders' => $completedOrders, 
            'cancelledOrders' => $cancelledOrders,
            'chartData' => $chartData,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'activities' => $activities,
        ]);
    }
} 
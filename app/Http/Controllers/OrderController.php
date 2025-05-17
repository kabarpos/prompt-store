<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Mail\UserCredentials;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display checkout page.
     */
    public function checkout()
    {
        // Debug: Log checkout request
        \Log::info('Checkout page request', [
            'user_id' => auth()->id(),
            'session_id' => Cart::getSessionId()
        ]);

        // Validasi user login
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk melakukan checkout');
        }

        $sessionId = Cart::getSessionId();
        
        $cartItems = Cart::with('product.category')
            ->where('session_id', $sessionId)
            ->get();
            
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong');
        }
        
        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
        
        $adminFee = 0; // Default admin fee, can be configurable
        $discount = 0;
        $coupon = session('coupon');
        
        // Jika ada kupon yang diterapkan
        if ($coupon) {
            $couponModel = \App\Models\Coupon::find($coupon['id']);
            if ($couponModel && $couponModel->isValid($subtotal)) {
                $discount = $couponModel->calculateDiscount($subtotal);
                $coupon['discount'] = $discount;
                session(['coupon' => $coupon]);
            } else {
                // Jika kupon sudah tidak valid, hapus dari session
                session()->forget('coupon');
                $coupon = null;
            }
        }
        
        $total = $subtotal + $adminFee - $discount;

        // Get user data
        $user = auth()->user();
        
        // Debug: Log data yang akan dikirim ke view
        \Log::info('Sending data to checkout view', [
            'user_id' => $user->id,
            'cart_items_count' => $cartItems->count(),
            'subtotal' => $subtotal,
            'total' => $total
        ]);
        
        return Inertia::render('public/orders/Checkout', [
            'cartItems' => $cartItems,
            'coupon' => $coupon,
            'summary' => [
                'subtotal' => $subtotal,
                'adminFee' => $adminFee,
                'discount' => $discount,
                'total' => $total,
                'itemCount' => $cartItems->sum('quantity'),
            ],
            'user' => $user
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        try {
            // Debug awal request
            \Log::channel('daily')->info('========== MULAI PROSES ORDER ==========');
            \Log::channel('daily')->info('Request data:', $request->except(['_token']));
            \Log::channel('daily')->info('Headers:', $request->headers->all());
            
            // ===== VALIDASI AWAL =====
            // Cek login
            if (!auth()->check()) {
                \Log::channel('daily')->error('ERROR: User tidak login');
                return back()->with('error', 'Silakan login terlebih dahulu untuk melakukan checkout');
            }
            
            // Validasi form dengan detail
            $validator = \Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'required|string|max:20',
            ]);
            
            if ($validator->fails()) {
                \Log::channel('daily')->error('VALIDASI GAGAL:', $validator->errors()->toArray());
                return back()->withErrors($validator)->withInput()
                    ->with('error', 'Validasi form gagal: ' . implode(', ', $validator->errors()->all()));
            }
            
            \Log::channel('daily')->info('Validasi form berhasil');
            
            // ===== DATA KERANJANG & SESSION =====
            $user = auth()->user();
            \Log::channel('daily')->info('User info:', [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);
            
            $sessionId = Cart::getSessionId();
            \Log::channel('daily')->info('Session ID:', ['session_id' => $sessionId]);
            
            // ===== AMBIL ITEM KERANJANG =====
            $cartItems = Cart::with('product')
                ->where('session_id', $sessionId)
                ->get();
            
            \Log::channel('daily')->info('Item keranjang:', [
                'jumlah' => $cartItems->count(),
                'items' => $cartItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'price' => $item->product->price,
                        'quantity' => $item->quantity
                    ];
                })->toArray()
            ]);
            
            if ($cartItems->isEmpty()) {
                \Log::channel('daily')->error('ERROR: Keranjang kosong');
                return back()->with('error', 'Keranjang belanja Anda kosong');
            }
            
            // ===== HITUNG TOTAL =====
            try {
                DB::beginTransaction();
                
                // Perhitungan subtotal
                $subtotal = 0;
                foreach ($cartItems as $item) {
                    $price = $item->product->price;
                    $qty = $item->quantity;
                    $lineTotal = $price * $qty;
                    $subtotal += $lineTotal;
                    
                    \Log::channel('daily')->info("Item: {$item->product->name} - Harga: {$price} x {$qty} = {$lineTotal}");
                }
                
                \Log::channel('daily')->info('Subtotal: ' . $subtotal);
                
                // Proses kupon
                $adminFee = 0;
                $discount = 0;
                $couponId = null;
                $couponCode = null;
                
                try {
                    // Proses kupon jika ada
                    $couponSession = session('coupon');
                    if ($couponSession && isset($couponSession['id'])) {
                        \Log::channel('daily')->info('Kupon dari session:', $couponSession);
                        
                        try {
                            $coupon = \App\Models\Coupon::find($couponSession['id']);
                            if ($coupon && $coupon->isValid($subtotal)) {
                                $discount = $coupon->calculateDiscount($subtotal);
                                $couponId = $coupon->id;
                                $couponCode = $coupon->code;
                                
                                $coupon->incrementUsage();
                                \Log::channel('daily')->info("Kupon {$couponCode} berhasil diterapkan. Diskon: {$discount}");
                            } else {
                                \Log::channel('daily')->warning('Kupon tidak valid untuk total belanja ini');
                            }
                        } catch (\Exception $e) {
                            \Log::channel('daily')->error('Error memproses kupon: ' . $e->getMessage());
                            // Abaikan error kupon, lanjutkan tanpa kupon
                            $discount = 0;
                            $couponId = null;
                            $couponCode = null;
                        }
                    } else {
                        \Log::channel('daily')->info('Tidak ada kupon yang diterapkan');
                    }
                } catch (\Exception $couponEx) {
                    \Log::channel('daily')->error('Error saat memproses kupon: ' . $couponEx->getMessage());
                    // Abaikan error kupon, lanjutkan tanpa kupon
                    $discount = 0;
                    $couponId = null; 
                    $couponCode = null;
                }
                
                // Pastikan nilai tidak null
                $total_after_discount = $subtotal - $discount;
                $total = $total_after_discount + $adminFee;
                
                \Log::channel('daily')->info("Perhitungan total: {$subtotal} - {$discount} + {$adminFee} = {$total}");
                
                // ===== BUAT ORDER =====
                // Generate nomor order
                $orderNumber = Order::generateOrderNumber();
                \Log::channel('daily')->info('Nomor order: ' . $orderNumber);
                
                // Data untuk order - PERBAIKI FIELD SESUAI DATABASE
                $orderData = [
                    'order_number' => $orderNumber,
                    'user_id' => $user->id,
                    'coupon_id' => $couponId, // Mungkin null
                    'coupon_code' => $couponCode, // Mungkin null
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email,
                    'subtotal' => $subtotal,
                    'discount' => $discount, // Field yang benar sesuai struktur DB
                    'admin_fee' => $adminFee,
                    'total_amount' => $total,
                    'status' => Order::STATUS_PENDING,
                    'notes' => $request->notes,
                ];
                
                // HANYA SIMPAN FIELD YANG ADA DI DATABASE
                // Hapus field yang tidak ada di tabel orders
                $tableColumns = Schema::getColumnListing('orders');
                $filteredOrderData = array_intersect_key($orderData, array_flip($tableColumns));
                
                \Log::channel('daily')->info('Data order yang akan dibuat (setelah filter):', $filteredOrderData);
                
                // Simpan order ke database dengan try-catch tambahan
                try {
                    $order = Order::create($filteredOrderData);
                    \Log::channel('daily')->info('Order berhasil dibuat dengan ID: ' . $order->id);
                } catch (\Exception $e) {
                    \Log::channel('daily')->error('ERROR KRITIS: Gagal menyimpan order ke database!');
                    \Log::channel('daily')->error('Exception: ' . $e->getMessage());
                    \Log::channel('daily')->error('Line: ' . $e->getLine() . ' di ' . $e->getFile());
                    \Log::channel('daily')->error('SQL Error: ' . $e->getMessage());
                    
                    // Coba cara alternatif jika ada masalah dengan Eloquent
                    try {
                        \Log::channel('daily')->info('Mencoba cara alternatif dengan DB::table');
                        $orderId = DB::table('orders')->insertGetId($filteredOrderData);
                        $order = Order::find($orderId);
                        
                        if (!$order) {
                            throw new \Exception('Order masih tidak dapat dibuat setelah alternatif');
                        }
                        
                        \Log::channel('daily')->info('Order berhasil dibuat dengan cara alternatif. ID: ' . $order->id);
                    } catch (\Exception $innerEx) {
                        \Log::channel('daily')->error('Cara alternatif juga gagal: ' . $innerEx->getMessage());
                        throw $innerEx; // Re-throw exception untuk di-handle di catch outer
                    }
                }
                
                // ===== BUAT ORDER ITEMS =====
                $orderItems = [];
                try {
                    foreach ($cartItems as $item) {
                        $orderItemData = [
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                            'quantity' => $item->quantity,
                            'subtotal' => $item->product->price * $item->quantity,
                        ];
                        
                        \Log::channel('daily')->info('Order item data: ', $orderItemData);
                        
                        $orderItem = OrderItem::create($orderItemData);
                        $orderItems[] = $orderItem;
                        
                        \Log::channel('daily')->info('Order item dibuat dengan ID: ' . $orderItem->id);
                    }
                } catch (\Exception $e) {
                    \Log::channel('daily')->error('ERROR: Gagal menyimpan order items!');
                    \Log::channel('daily')->error('Exception: ' . $e->getMessage());
                    \Log::channel('daily')->error('Line: ' . $e->getLine() . ' di ' . $e->getFile());
                    throw $e; // Re-throw untuk ditangani di catch utama
                }
                
                // ===== HAPUS KERANJANG =====
                try {
                    $deletedCount = Cart::where('session_id', $sessionId)->delete();
                    \Log::channel('daily')->info('Keranjang berhasil dihapus. Item terhapus: ' . $deletedCount);
                    
                    // Hapus session kupon
                    session()->forget('coupon');
                    \Log::channel('daily')->info('Session kupon dihapus');
                } catch (\Exception $e) {
                    \Log::channel('daily')->warning('Peringatan: Gagal menghapus keranjang: ' . $e->getMessage());
                    // Tidak throw exception di sini karena order sudah terbuat
                }
                
                // ===== COMMIT TRANSACTION & REDIRECT =====
                DB::commit();
                \Log::channel('daily')->info('DB Transaction berhasil di-commit');
                
                // Cache data order untuk fallback
                \Cache::put('last_order_id', $order->id, now()->addHour());
                \Cache::put('last_order_number', $order->order_number, now()->addHour());
                \Cache::put('last_order_email', $request->customer_email, now()->addHour());
                
                // Kirim WhatsApp notifikasi
                try {
                    app(WhatsAppService::class)->sendOrderCreatedNotification($order);
                    \Log::channel('daily')->info('Notifikasi WhatsApp berhasil dikirim', [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]);
                } catch (\Exception $e) {
                    \Log::channel('daily')->error('Gagal mengirim notifikasi WhatsApp', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                    // Tidak throw exception untuk tidak menggagalkan order
                }
                
                // Data untuk halaman thank you
                $orderId = $order->id;
                $redirectUrl = route('orders.thankyou', ['order' => $orderId]);
                
                \Log::channel('daily')->info('============ ORDER BERHASIL ============');
                \Log::channel('daily')->info('Redirect ke: ' . $redirectUrl);
                
                // Return dengan semua data yang diperlukan
                return redirect($redirectUrl)
                    ->with('order', $order)
                    ->with('order_id', $orderId)
                    ->with('order_number', $order->order_number)
                    ->with('redirect_url', $redirectUrl)
                    ->with('success', 'Pesanan berhasil dibuat dan sedang diproses!');
            } catch (\Exception $dbEx) {
                // Rollback jika ada exception di database transaction
                DB::rollBack();
                \Log::channel('daily')->error('DATABASE TRANSACTION ERROR:');
                \Log::channel('daily')->error('Message: ' . $dbEx->getMessage());
                \Log::channel('daily')->error('Line: ' . $dbEx->getLine() . ' di ' . $dbEx->getFile());
                \Log::channel('daily')->error('SQL: ' . method_exists($dbEx, 'getSql') ? $dbEx->getSql() : 'N/A');
                \Log::channel('daily')->error('Stack trace: ' . $dbEx->getTraceAsString());
                
                return back()->with('error', 'Database error: ' . $dbEx->getMessage());
            }
        } catch (\Exception $outerEx) {
            // Catch untuk exception di luar scope database transaction
            \Log::channel('daily')->error('FATAL ERROR:');
            \Log::channel('daily')->error('Message: ' . $outerEx->getMessage());
            \Log::channel('daily')->error('Line: ' . $outerEx->getLine() . ' di ' . $outerEx->getFile());
            \Log::channel('daily')->error('Stack trace: ' . $outerEx->getTraceAsString());
            \Log::channel('daily')->error('========== PROSES ORDER GAGAL ==========');
            
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $outerEx->getMessage());
        }
    }

    /**
     * Display thank you page after successful order.
     */
    public function thankYou($orderId)
    {
        $order = Order::with(['items.product', 'payment.paymentMethod'])->findOrFail($orderId);
        
        // Ambil semua metode pembayaran yang aktif
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        return Inertia::render('public/cart/ThankYou', [
            'order' => $order,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Display order tracking page.
     */
    public function trackOrder($order)
    {
        $orderModel = Order::with(['items.product', 'payment.paymentMethod'])
            ->where('order_number', $order)
            ->first();
            
        // Ambil semua metode pembayaran yang aktif
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
            
        if ($orderModel) {
            return Inertia::render('public/orders/Track', [
                'order' => $orderModel,
                'found' => true,
                'paymentMethods' => $paymentMethods
            ]);
        }
        
        return Inertia::render('public/orders/Track', [
            'found' => false,
            'message' => 'Pesanan tidak ditemukan. Silakan periksa kembali nomor pesanan Anda.'
        ]);
    }

    /**
     * Display a listing of the user's orders.
     */
    public function userOrders(Request $request)
    {
        $user = auth()->user();
        
        $orders = Order::with(['items.product', 'payment.paymentMethod'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
            
        return Inertia::render('user/orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Display the specified order details.
     */
    public function show(Order $order)
    {
        // Make sure user can only view their own orders
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }
        
        $order->load(['items.product', 'payment.paymentMethod']);
        
        return Inertia::render('user/orders/Show', [
            'order' => $order
        ]);
    }
}

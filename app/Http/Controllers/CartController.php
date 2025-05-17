<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct()
    {
        // Hapus middleware auth karena kita ingin mendukung guest users
        // $this->middleware('auth');
    }

    /**
     * Display the cart page.
     */
    public function index()
    {
        $sessionId = Cart::getSessionId();
        
        $cartItems = Cart::with('product.category')
            ->where('session_id', $sessionId)
            ->get();
            
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
                $coupon['formattedDiscount'] = 'Rp ' . number_format($discount, 0, ',', '.');
                
                // Pastikan name selalu ada
                if (!isset($coupon['name']) || empty($coupon['name'])) {
                    $coupon['name'] = $coupon['code'];
                }
                
                session(['coupon' => $coupon]);
                
                // Log informasi kupon untuk debugging
                \Log::info('Kupon yang digunakan pada cart:', [
                    'coupon_data' => $coupon,
                    'discount_calculated' => $discount
                ]);
            } else {
                // Jika kupon sudah tidak valid, hapus dari session
                session()->forget('coupon');
                $coupon = null;
            }
        }
        
        $total = $subtotal + $adminFee - $discount;
        
        return Inertia::render('public/cart/Index', [
            'cartItems' => $cartItems,
            'coupon' => $coupon,
            'summary' => [
                'subtotal' => $subtotal,
                'adminFee' => $adminFee,
                'discount' => $discount,
                'total' => $total,
                'itemCount' => $cartItems->sum('quantity'),
            ]
        ]);
    }

    /**
     * Add a product to cart.
     */
    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $sessionId = Cart::getSessionId();
        $product = Product::findOrFail($request->product_id);
        
        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak tersedia'
            ], 400);
        }
        
        // Check if item is already in cart
        $cartItem = Cart::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->first();
            
        if ($cartItem) {
            // Update quantity if already in cart
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            // Create new cart item
            $cartItem = Cart::create([
                'session_id' => $sessionId,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'user_id' => auth()->id() // Will be null for guests
            ]);
        }
        
        // Get updated cart count
        $cartCount = Cart::where('session_id', $sessionId)->sum('quantity');
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function updateQuantity(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $sessionId = Cart::getSessionId();
        $cartItem = Cart::where('session_id', $sessionId)
            ->where('id', $id)
            ->first();
            
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        $cartItem->update([
            'quantity' => $request->quantity
        ]);
        
        // Get updated cart count
        $cartCount = Cart::where('session_id', $sessionId)->sum('quantity');
        
        return response()->json([
            'success' => true,
            'message' => 'Kuantitas berhasil diperbarui',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Remove a product from cart.
     */
    public function removeFromCart($id)
    {
        $sessionId = Cart::getSessionId();
        $cartItem = Cart::where('session_id', $sessionId)
            ->where('id', $id)
            ->first();
            
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan'
            ], 404);
        }
        
        $cartItem->delete();
        
        // Get updated cart count
        $cartCount = Cart::where('session_id', $sessionId)->sum('quantity');
        
        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Clear the cart.
     */
    public function clearCart()
    {
        $sessionId = Cart::getSessionId();
        
        Cart::where('session_id', $sessionId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan',
            'cart_count' => 0
        ]);
    }

    /**
     * Get cart count (for header display).
     */
    public function getCartCount()
    {
        $sessionId = Cart::getSessionId();
        $count = Cart::where('session_id', $sessionId)->sum('quantity');
        
        return response()->json([
            'count' => $count
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    /**
     * Apply coupon to the cart.
     */
    public function apply(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|exists:coupons,code',
            ], [
                'code.exists' => 'Kode kupon tidak valid',
            ]);

            $couponCode = $request->code;
            $coupon = Coupon::where('code', $couponCode)->first();
            
            if (!$coupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode kupon tidak ditemukan',
                ], 404);
            }

            // Pastikan kupon aktif
            if (!$coupon->is_active) {
                // Debug: Log status kupon yang tidak aktif
                \Log::warning('Kupon tidak aktif, mencoba mengaktifkan: ' . $couponCode, [
                    'coupon_id' => $coupon->id,
                    'is_active' => $coupon->is_active,
                    'status_text' => $coupon->status_text
                ]);
                
                // Perbaikan: Coba aktifkan kupon
                $coupon->is_active = true;
                $coupon->save();
                
                // Reload kupon dari database untuk memastikan perubahan tersimpan
                $coupon->refresh();
                
                \Log::info('Status kupon setelah diaktifkan: ' . $couponCode, [
                    'coupon_id' => $coupon->id,
                    'is_active_after' => $coupon->is_active,
                    'status_text_after' => $coupon->status_text
                ]);
                
                // Jika masih tidak aktif, kembalikan error
                if (!$coupon->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kupon tidak aktif dan tidak dapat diaktifkan',
                    ], 400);
                }
            }

            // Hitung subtotal dari keranjang untuk validasi kupon
            $sessionId = Cart::getSessionId();
            
            // Debug untuk session ID
            \Log::info('Session ID saat menerapkan kupon:', [
                'session_id' => $sessionId,
                'laravel_session' => $request->session()->getId(),
                'authenticated' => auth()->check() ? 'yes' : 'no',
                'user_id' => auth()->check() ? auth()->id() : 'guest'
            ]);
            
            $cartItems = Cart::with('product')
                ->where('session_id', $sessionId)
                ->get();
                
            \Log::info('Cart items saat validasi kupon:', [
                'count' => $cartItems->count(),
                'item_ids' => $cartItems->pluck('id')->toArray()
            ]);
            
            $subtotal = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            // Validasi kupon
            if (!$coupon->isValid($subtotal)) {
                $message = 'Kupon tidak dapat digunakan';
                
                if (!$coupon->is_active) {
                    $message = 'Kupon tidak aktif';
                } elseif ($coupon->starts_at && now()->isBefore($coupon->starts_at)) {
                    $message = 'Kupon belum berlaku';
                } elseif ($coupon->expires_at && now()->isAfter($coupon->expires_at)) {
                    $message = 'Kupon sudah kadaluarsa';
                } elseif ($coupon->max_uses > 0 && $coupon->used_count >= $coupon->max_uses) {
                    $message = 'Kupon sudah mencapai batas penggunaan';
                } elseif ($subtotal < $coupon->min_purchase) {
                    $message = 'Minimal pembelian Rp ' . number_format($coupon->min_purchase, 0, ',', '.') . ' untuk menggunakan kupon ini';
                }
                
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 400);
            }

            // Hitung diskon
            $discount = $coupon->calculateDiscount($subtotal);
            
            // Simpan kupon ke session
            Session::put('coupon', [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->code,
                'description' => $coupon->description,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount' => $discount,
            ]);
            
            // Log informasi tentang session
            \Log::info('Coupon disimpan ke session:', [
                'session_id' => $request->session()->getId(),
                'coupon_data' => Session::get('coupon')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kupon berhasil diterapkan',
                'coupon' => [
                    'code' => $coupon->code,
                    'name' => $coupon->code,
                    'description' => $coupon->description,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'discount' => $discount,
                    'formattedDiscount' => 'Rp ' . number_format($discount, 0, ',', '.'),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menerapkan kupon: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengaplikasikan kupon: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove coupon from cart.
     */
    public function remove(Request $request)
    {
        try {
            Session::forget('coupon');
            
            return response()->json([
                'success' => true,
                'message' => 'Kupon berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat menghapus kupon: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kupon',
            ], 500);
        }
    }
}

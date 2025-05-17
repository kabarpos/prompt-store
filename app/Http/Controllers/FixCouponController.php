<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixCouponController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            // Reset semua status kupon ke aktif
            $coupons = Coupon::all();
            
            $results = [];
            
            foreach ($coupons as $coupon) {
                $oldActive = $coupon->is_active;
                
                // Coba ubah status kupon dengan query database langsung
                DB::table('coupons')
                    ->where('id', $coupon->id)
                    ->update(['is_active' => true]);
                
                // Reload untuk mendapatkan nilai terbaru
                $coupon->refresh();
                
                $results[] = [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'old_active' => $oldActive,
                    'new_active' => $coupon->is_active,
                    'status_text' => $coupon->status_text
                ];
                
                // Jika ada session coupon dengan ID ini, update juga
                if ($request->session()->has('coupon') && $request->session()->get('coupon')['id'] == $coupon->id) {
                    $sessionCoupon = $request->session()->get('coupon');
                    $sessionCoupon['is_active'] = true;
                    $request->session()->put('coupon', $sessionCoupon);
                    
                    $results[] = [
                        'session_updated' => true,
                        'session_coupon' => $sessionCoupon
                    ];
                }
            }
            
            // Cek apakah ada lagi data kupon di session
            if ($request->session()->has('coupon')) {
                $sessionData = $request->session()->get('coupon');
                $results[] = [
                    'session_data_exists' => true,
                    'session_data' => $sessionData
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status kupon berhasil diperbaiki',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat memperbaiki status kupon: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}

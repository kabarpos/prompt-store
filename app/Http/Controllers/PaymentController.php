<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    /**
     * Display payment options for an order
     */
    public function showOptions(Order $order)
    {
        // Make sure user can only access their own orders
        if (auth()->id() !== $order->user_id && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        
        $order->load(['items.product', 'payment']);
        
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return Inertia::render('user/orders/Payment', [
            'order' => $order,
            'paymentMethods' => $paymentMethods
        ]);
    }
    
    /**
     * Update payment method for an order
     */
    public function updateMethod(Request $request, Order $order)
    {
        // Make sure user can only update their own orders
        if (auth()->id() !== $order->user_id && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);
        
        // Get or create payment
        $payment = Payment::firstOrNew(['order_id' => $order->id]);
        $payment->payment_method_id = $request->payment_method_id;
        $payment->amount = $order->total_amount;
        $payment->status = 'pending';
        $payment->save();
        
        return redirect()->back()->with('success', 'Metode pembayaran berhasil diperbarui');
    }
    
    /**
     * Get available payment methods
     */
    public function getPaymentMethods()
    {
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return response()->json($paymentMethods);
    }
}

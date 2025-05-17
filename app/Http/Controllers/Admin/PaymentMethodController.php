<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::withCount('payments')
            ->orderBy('sort_order')
            ->paginate(10);

        return Inertia::render('admin/payment-methods/Index', [
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/payment-methods/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code',
            'type' => 'required|string|in:bank_transfer,payment_gateway',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'sort_order' => $request->sort_order ?? 0,
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('payment-methods', 'public');
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        $paymentMethod->loadCount('payments');
        
        return Inertia::render('admin/payment-methods/Show', [
            'paymentMethod' => $paymentMethod
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return Inertia::render('admin/payment-methods/Edit', [
            'paymentMethod' => $paymentMethod
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $paymentMethod->id,
            'type' => 'required|string|in:bank_transfer,payment_gateway',
            'account_number' => 'nullable|string|max:50',
            'account_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer',
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'type' => $request->type,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'description' => $request->description,
            'is_active' => $request->is_active,
            'sort_order' => $request->sort_order ?? $paymentMethod->sort_order,
        ];

        if ($request->hasFile('logo')) {
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            $data['logo'] = $request->file('logo')->store('payment-methods', 'public');
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            // Check if there are related payments
            if ($paymentMethod->payments()->count() > 0) {
                return redirect()->route('admin.payment-methods.index')
                    ->with('error', 'Metode pembayaran tidak dapat dihapus karena sudah digunakan dalam transaksi pembayaran.');
            }
            
            // Delete logo if exists
            if ($paymentMethod->logo) {
                Storage::disk('public')->delete($paymentMethod->logo);
            }
            
            $paymentMethod->delete();
            
            return redirect()->route('admin.payment-methods.index')
                ->with('success', 'Metode pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.payment-methods.index')
                ->with('error', 'Gagal menghapus metode pembayaran: ' . $e->getMessage());
        }
    }
}

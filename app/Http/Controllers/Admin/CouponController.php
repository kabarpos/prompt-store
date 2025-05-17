<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('manage_settings');

        $coupons = Coupon::latest()->get();

        return Inertia::render('admin/coupons/Index', [
            'coupons' => $coupons,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('manage_settings');

        return Inertia::render('admin/coupons/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('manage_settings');

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Validasi tambahan untuk persentase
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Persentase diskon tidak boleh lebih dari 100%']);
        }

        $coupon = Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $this->authorize('manage_settings');

        $coupon->load('orders');

        return Inertia::render('admin/coupons/Show', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $this->authorize('manage_settings');

        return Inertia::render('admin/coupons/Edit', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $this->authorize('manage_settings');

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_purchase' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'max_uses' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Validasi tambahan untuk persentase
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withErrors(['value' => 'Persentase diskon tidak boleh lebih dari 100%']);
        }

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $this->authorize('manage_settings');

        // Cek apakah kupon pernah digunakan
        if ($coupon->orders()->exists()) {
            return back()->with('error', 'Kupon tidak dapat dihapus karena sudah pernah digunakan');
        }

        $coupon->delete();

        return back()->with('success', 'Kupon berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(Coupon $coupon)
    {
        $this->authorize('manage_settings');

        $coupon->update([
            'is_active' => !$coupon->is_active,
        ]);

        return back()->with('success', 'Status kupon berhasil diubah');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminWhatsappController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $whatsappAdmins = AdminWhatsapp::orderBy('order')->get();
        
        return Inertia::render('admin/settings/whatsapp/Index', [
            'whatsappAdmins' => $whatsappAdmins,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:admin_whatsapps',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);
        
        // Set order as last item + 1
        $lastOrder = AdminWhatsapp::max('order') ?? 0;
        $validated['order'] = $lastOrder + 1;
        
        $whatsapp = AdminWhatsapp::create($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Nomor WhatsApp berhasil ditambahkan',
                'data' => $whatsapp
            ], 201);
        }
        
        return redirect()->back()
            ->with('success', 'Nomor WhatsApp berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdminWhatsapp $whatsapp)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('admin_whatsapps')->ignore($whatsapp->id),
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $whatsapp->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Nomor WhatsApp berhasil diperbarui',
                'data' => $whatsapp
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Nomor WhatsApp berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminWhatsapp $whatsapp)
    {
        $whatsapp->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Nomor WhatsApp berhasil dihapus'
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Nomor WhatsApp berhasil dihapus');
    }
} 
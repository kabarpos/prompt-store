<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialMedia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SocialMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $socialMedia = SocialMedia::orderBy('order')->get();
        
        return Inertia::render('admin/settings/social-media/Index', [
            'socialMedia' => $socialMedia,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'username' => 'required|string|max:255',
            'url' => [
                'required',
                'url',
                'max:255',
                Rule::unique('social_media'),
            ],
            'is_active' => 'boolean',
        ]);
        
        // Set order as last item + 1
        $lastOrder = SocialMedia::max('order') ?? 0;
        $validated['order'] = $lastOrder + 1;
        
        $socialMedia = SocialMedia::create($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Media sosial berhasil ditambahkan',
                'data' => $socialMedia
            ], 201);
        }
        
        return redirect()->back()
            ->with('success', 'Media sosial berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialMedia $socialMedia)
    {
        $validated = $request->validate([
            'platform' => 'required|string|max:50',
            'username' => 'required|string|max:255',
            'url' => [
                'required',
                'url',
                'max:255',
                Rule::unique('social_media')->ignore($socialMedia->id),
            ],
            'is_active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);
        
        $socialMedia->update($validated);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Media sosial berhasil diperbarui',
                'data' => $socialMedia
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Media sosial berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialMedia $socialMedia)
    {
        $socialMedia->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Media sosial berhasil dihapus'
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Media sosial berhasil dihapus');
    }
} 
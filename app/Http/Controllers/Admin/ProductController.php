<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Filter by search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('name', 'like', "%{$searchTerm}%");
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $isActive = $request->status == '1';
            $query->where('is_active', $isActive);
        }
        
        $products = $query->latest()->paginate(10);
        
        return Inertia::render('admin/products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('admin/products/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'description' => 'required|string',
                'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'custom_url' => 'nullable|string|max:255',
                'demo_url' => 'nullable|url|max:255',
                'product_features' => 'nullable|json',
                'product_values' => 'nullable|json',
            ]);

            $featuredImage = $request->file('featured_image')->store('products', 'public');
            
            // Generate product code
            $latestProduct = Product::latest()->first();
            $latestCode = $latestProduct ? $latestProduct->product_code : null;
            $newCode = $this->generateProductCode($latestCode);

            $product = Product::create([
                'name' => $request->name,
                'slug' => empty($request->custom_url) ? Str::slug($request->name) : Str::slug($request->custom_url),
                'category_id' => $request->category_id ?: null,
                'price' => $request->price,
                'description' => $request->description,
                'featured_image' => $featuredImage,
                'custom_url' => $request->custom_url,
                'demo_url' => $request->demo_url,
                'product_features' => $request->product_features ? json_decode($request->product_features) : null,
                'product_values' => $request->product_values ? json_decode($request->product_values) : null,
                'product_code' => $newCode,
                'is_active' => true, // Default to active
            ]);

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $image) {
                    $path = $image->store('products/gallery', 'public');
                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image' => $path,
                        'sort_order' => $index
                    ]);
                }
            }

            // Load relationships
            $product->load('gallery', 'category');

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dibuat',
                    'product' => $product
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dibuat');
        
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat produk',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal membuat produk: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'gallery');

        return Inertia::render('admin/products/Show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load('gallery');
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('admin/products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        try {
            // TAMBAHKAN LOG DEBUG DI AWAL METHOD
            \Log::debug('UPDATE PRODUCT REQUEST:', [
                'is_digital' => $request->is_digital,
                'has_hidden_content' => $request->has_hidden_content,
                'hidden_content_length' => strlen($request->hidden_content ?? ''),
                'digital_file' => $request->hasFile('digital_file') ? 'ADA FILE' : 'TIDAK ADA FILE',
                'all_data' => $request->all()
            ]);

            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'nullable|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'description' => 'required|string',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'custom_url' => 'nullable|string|max:255',
                'demo_url' => 'nullable|url|max:255',
                'product_features' => 'nullable|json',
                'product_values' => 'nullable|json',
                'is_active' => 'nullable|in:0,1,true,false',
                // Tambahan validasi untuk digital product
                'is_digital' => 'nullable|boolean',
                'has_hidden_content' => 'nullable|boolean',
                'hidden_content' => 'nullable|string',
                'download_limit' => 'nullable|integer|min:0',
                'access_days' => 'nullable|integer|min:0',
                'digital_file' => 'nullable|file|max:25600', // Max 25MB
            ]);

            // TANGKAP FIELD DIGITAL EXPLICITLY
            $data = $request->all();
            
            // PASTIKAN BOOLEAN DIPROSES BENAR
            $data['is_digital'] = filter_var($request->is_digital, FILTER_VALIDATE_BOOLEAN);
            $data['has_hidden_content'] = filter_var($request->has_hidden_content, FILTER_VALIDATE_BOOLEAN);
            $data['is_active'] = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);
            
            // TANGANI FILE DIGITAL TERLEBIH DAHULU
            if ($request->hasFile('digital_file')) {
                $path = $request->file('digital_file')->store('products/digital', 'private');
                $data['digital_file_path'] = $path;
                \Log::debug("File digital tersimpan: {$path}");
            }
            
            // Debug nilai custom_url saat ini dan di request
            \Log::info('Update produk - custom_url:', [
                'current_custom_url' => $product->custom_url,
                'request_custom_url' => $request->custom_url,
                'has_custom_url' => $request->has('custom_url'),
                'is_empty' => empty($request->custom_url)
            ]);
            
            // Custom URL hanya diupdate jika field ada dalam request dan memiliki nilai yang valid
            if ($request->has('custom_url')) {
                if (!empty($request->custom_url)) {
                    // Jika ada nilai valid, update custom_url dan slug
                    $data['custom_url'] = $request->custom_url;
                    $data['slug'] = Str::slug($request->custom_url);
                } else {
                    // Jika field ada dalam request tapi kosong, pertahankan nilai lama
                    // TIDAK PERLU mengupdate, karena kita ingin mempertahankan nilai yang sudah ada
                    unset($data['custom_url']); // Pastikan custom_url tidak dimasukkan dalam update
                }
            }
            
            // Update slug berdasarkan nama hanya jika tidak ada custom_url yang diisi
            // dan jika nama produk berubah
            if (!isset($data['slug']) && $product->name !== $request->name && empty($request->custom_url)) {
                $data['slug'] = Str::slug($request->name);
            }

            // Proses gambar utama jika ada
            if ($request->hasFile('featured_image')) {
                // Hapus gambar lama jika ada
                if ($product->featured_image) {
                    Storage::disk('public')->delete($product->featured_image);
                }
                $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
            }

            // Log data sebelum update
            \Log::debug('DATA YANG AKAN DIUPDATE:', [
                'is_digital' => $data['is_digital'],
                'has_hidden_content' => $data['has_hidden_content'],
                'hidden_content' => $data['hidden_content'] ? 'Ada isi' : 'Kosong',
                'download_limit' => $data['download_limit'] ?? 0,
                'access_days' => $data['access_days'] ?? 0,
                'digital_file_path' => $data['digital_file_path'] ?? 'Tidak ada file baru'
            ]);
            
            // Update produk
            $product->update($data);
            
            // Verifikasi data tersimpan
            $product->refresh();
            \Log::debug('SETELAH UPDATE - VERIFIKASI DATA TERSIMPAN:', [
                'is_digital' => $product->is_digital,
                'has_hidden_content' => $product->has_hidden_content,
                'hidden_content' => $product->hidden_content ? 'Ada isi' : 'Kosong',
                'download_limit' => $product->download_limit,
                'access_days' => $product->access_days,
                'digital_file_path' => $product->digital_file_path
            ]);

            // SOLUSI TERAKHIR - UPDATE DATABASE LANGSUNG
            try {
                \DB::table('products')->where('id', $product->id)->update([
                    'is_digital' => $data['is_digital'] ? 1 : 0,
                    'has_hidden_content' => $data['has_hidden_content'] ? 1 : 0,
                    'hidden_content' => $data['hidden_content'] ?? null,
                    'download_limit' => $data['download_limit'] ?? 0,
                    'access_days' => $data['access_days'] ?? 0
                ]);
                
                \Log::debug("Update database langsung berhasil");
            } catch (\Exception $e) {
                \Log::error("GAGAL UPDATE DATABASE LANGSUNG: " . $e->getMessage());
            }

            // Proses gambar galeri jika ada
            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    ProductGallery::create([
                        'product_id' => $product->id,
                        'image' => $image->store('products/gallery', 'public'),
                        'sort_order' => $product->gallery()->count()
                    ]);
                }
            }

            // Refresh product data with gallery
            $product->refresh();
            $product->load('gallery', 'category');

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil diperbarui',
                    'product' => $product
                ]);
            }

            return redirect()->back()
                ->with('success', 'Produk berhasil diperbarui');
                
        } catch (\Exception $e) {
            \Log::error('ERROR UPDATE PRODUK: ' . $e->getMessage());
            \Log::error('STACK TRACE: ' . $e->getTraceAsString());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui produk',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            // Hapus gambar utama
            if ($product->featured_image) {
                Storage::disk('public')->delete($product->featured_image);
            }
            
            // Hapus semua gambar galeri
            foreach ($product->gallery as $gallery) {
                Storage::disk('public')->delete($gallery->image);
                $gallery->delete();
            }
            
            // Hapus produk (menggunakan soft delete)
            $product->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil dihapus'
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus');
                
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus produk',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }

    public function deleteGalleryImage(ProductGallery $gallery)
    {
        try {
            $productId = $gallery->product_id;
            
            // Hapus file gambar
            Storage::disk('public')->delete($gallery->image);
            
            // Hapus record dari database
            $gallery->delete();

            // Ambil produk yang direfresh dengan gallery baru
            $product = Product::with('gallery', 'category')->find($productId);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gambar galeri berhasil dihapus',
                    'product' => $product
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Gambar galeri berhasil dihapus');
                
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus gambar',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus gambar: ' . $e->getMessage()]);
        }
    }

    public function updateGalleryOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:product_galleries,id',
            'items.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->items as $item) {
            ProductGallery::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Gallery order updated successfully.']);
    }

    /**
     * Generate a unique product code with format DLXXX
     */
    private function generateProductCode($latestCode = null)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 3;
        
        do {
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            $productCode = 'DL' . $randomString;
            
            // Cek keunikan kode produk
            $exists = Product::withTrashed()->where('product_code', $productCode)->exists();
            
        } while ($exists); // Ulangi jika kode sudah digunakan
        
        return $productCode;
    }
}

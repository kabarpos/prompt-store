<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsController extends Controller
{
    /**
     * Display a listing of the product.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->latest();
        
        // Filter berdasarkan kategori jika ada
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        
        return Inertia::render('public/products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only('category', 'search')
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show($slug)
    {
        // Coba cari produk berdasarkan slug
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'gallery'])
            ->firstOrFail();
        
        // Produk terkait (dari kategori yang sama)
        $relatedProducts = [];
        if ($product->category_id) {
            $relatedProducts = Product::where('id', '!=', $product->id)
                ->where('category_id', $product->category_id)
                ->where('is_active', true)
                ->limit(4)
                ->get();
        }
        
        // Jika relasi masih kosong, ambil produk terbaru
        if (count($relatedProducts) === 0) {
            $relatedProducts = Product::where('id', '!=', $product->id)
                ->where('is_active', true)
                ->latest()
                ->limit(4)
                ->get();
        }
        
        return Inertia::render('public/products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
} 
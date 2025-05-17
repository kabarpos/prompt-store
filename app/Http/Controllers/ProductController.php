<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected $imageOptimizer;

    public function __construct(ImageOptimizer $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categorySlug = $request->input('category');
        
        $query = Product::query()->with(['category', 'gallery'])->where('is_active', true);
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($categorySlug) {
            $query->whereHas('category', function($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        $products = $query->orderBy('price')->paginate(12)->withQueryString();
        
        $categories = Cache::remember('categories', 3600, function () {
            return Category::orderBy('name')->get();
        });
        
        return Inertia::render('public/products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category'])
        ]);
    }

    public function show($slug)
    {
        $product = Product::getProductBySlug($slug);
        
        // Tambahkan related products
        $relatedProducts = [];
        if ($product->category_id) {
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->with('category')
                ->take(4)
                ->get();
        }
        
        return Inertia::render('public/products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'featured_image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $path = 'products/' . time() . '_' . $image->getClientOriginalName();
            
            $this->imageOptimizer->storeOptimized(
                $image,
                $path,
                800, // width
                600, // height
                80   // quality
            );
            
            $validated['featured_image'] = $path;
        }

        $product = Product::create($validated);
        
        return redirect()->route('products.show', $product->slug)
            ->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($product->featured_image) {
                Storage::delete($product->featured_image);
            }

            $image = $request->file('featured_image');
            $path = 'products/' . time() . '_' . $image->getClientOriginalName();
            
            $this->imageOptimizer->storeOptimized(
                $image,
                $path,
                800,
                600,
                80
            );
            
            $validated['featured_image'] = $path;
        }

        $product->update($validated);
        
        return redirect()->route('products.show', $product->slug)
            ->with('success', 'Product updated successfully.');
    }
} 
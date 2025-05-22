<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\ProductGallery;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'featured_image' => 'required|image|max:2048',
            'product_features' => 'nullable|array',
            'product_values' => 'nullable|array',
            'is_digital' => 'boolean',
            'has_hidden_content' => 'boolean',
            'hidden_content' => 'nullable|required_if:has_hidden_content,1',
            'digital_file' => 'nullable|required_if:is_digital,1,has_hidden_content,0|file|max:25600', // 25MB max
            'download_limit' => 'nullable|integer|min:0',
            'access_days' => 'nullable|integer|min:0',
        ]);

        // Process featured image
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('products/images', 'public');
        }
        
        // Process digital file if product is digital
        $digitalFilePath = null;
        if ($request->is_digital && $request->hasFile('digital_file')) {
            $digitalFilePath = $request->file('digital_file')->store('products/digital', 'local');
        }

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->is_active = $request->is_active ?? false;
        $product->featured_image = $featuredImagePath ? '/storage/' . $featuredImagePath : null;
        $product->product_features = $request->product_features;
        $product->product_values = $request->product_values;
        $product->custom_url = $request->custom_url;
        $product->demo_url = $request->demo_url;
        
        // Set digital product fields
        $product->is_digital = $request->is_digital ?? false;
        $product->digital_file_path = $digitalFilePath;
        $product->download_limit = $request->download_limit;
        $product->access_days = $request->access_days;
        
        // Set hidden content fields
        $product->has_hidden_content = $request->has_hidden_content ?? false;
        if ($product->has_hidden_content) {
            $product->hidden_content = $request->hidden_content;
        }
        
        $product->save();

        // Save product galleries if any
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $image) {
                $imagePath = $image->store('products/galleries', 'public');
                
                ProductGallery::create([
                    'product_id' => $product->id,
                    'image' => '/storage/' . $imagePath,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'featured_image' => 'required|image|max:2048',
            'product_features' => 'nullable|array',
            'product_values' => 'nullable|array',
            'is_digital' => 'boolean',
            'has_hidden_content' => 'boolean',
            'hidden_content' => 'nullable|required_if:has_hidden_content,1',
            'digital_file' => 'nullable|required_if:is_digital,1,has_hidden_content,0|file|max:25600', // 25MB max
            'download_limit' => 'nullable|integer|min:0',
            'access_days' => 'nullable|integer|min:0',
        ]);

        // Process featured image if uploaded
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($product->featured_image) {
                $oldPath = str_replace('/storage/', '', $product->featured_image);
                Storage::disk('public')->delete($oldPath);
            }
            
            $featuredImagePath = $request->file('featured_image')->store('products/images', 'public');
            $product->featured_image = '/storage/' . $featuredImagePath;
        }
        
        // Process digital file if uploaded
        if ($request->is_digital && $request->hasFile('digital_file')) {
            // Delete old file if exists
            if ($product->digital_file_path) {
                Storage::delete($product->digital_file_path);
            }
            
            $product->digital_file_path = $request->file('digital_file')->store('products/digital', 'local');
        }

        $product->name = $request->name;
        // Only update slug if custom_url is not set
        if (empty($request->custom_url)) {
            $product->slug = Str::slug($request->name);
        }
        $product->price = $request->price;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->is_active = $request->is_active ?? false;
        $product->product_features = $request->product_features;
        $product->product_values = $request->product_values;
        $product->custom_url = $request->custom_url;
        $product->demo_url = $request->demo_url;
        
        // Update digital product fields
        $product->is_digital = $request->is_digital ?? false;
        $product->download_limit = $request->download_limit;
        $product->access_days = $request->access_days;
        
        // Update hidden content fields
        $product->has_hidden_content = $request->has_hidden_content ?? false;
        if ($product->has_hidden_content) {
            $product->hidden_content = $request->hidden_content;
        }
        
        $product->save();

        // Save additional galleries if any
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $image) {
                $imagePath = $image->store('products/galleries', 'public');
                
                ProductGallery::create([
                    'product_id' => $product->id,
                    'image' => '/storage/' . $imagePath,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }
} 
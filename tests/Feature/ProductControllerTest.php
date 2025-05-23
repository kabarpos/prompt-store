<?php

use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('product index page displays correctly', function () {
    // Buat beberapa produk
    Product::factory()->count(3)->create();
    
    // Akses halaman index produk
    $response = $this->get(route('products.index'));
    
    // Cek response
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')
            ->has('products.data', 3)
        );
});

test('guest can view product detail page', function () {
    // Buat produk
    $product = Product::factory()->create();
    
    // Akses halaman detail produk
    $response = $this->get(route('products.show', $product));
    
    // Cek response
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Products/Show')
            ->has('product', fn (Assert $page) => $page
                ->where('id', $product->id)
                ->where('name', $product->name)
                ->etc()
            )
        );
});

test('admin can create new product', function () {
    // Login sebagai admin
    $admin = User::factory()->create()->assignRole('admin');
    $this->actingAs($admin);
    
    // Data produk baru
    $productData = [
        'name' => 'New Product',
        'description' => 'Product Description',
        'price' => 50000,
        'status' => 'published',
        'category_id' => 1,
    ];
    
    // Submit data produk baru
    $response = $this->post(route('admin.products.store'), $productData);
    
    // Cek database
    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'price' => 50000,
    ]);
    
    // Cek redirect
    $response->assertRedirect(route('admin.products.index'));
});

test('admin can update existing product', function () {
    // Login sebagai admin
    $admin = User::factory()->create()->assignRole('admin');
    $this->actingAs($admin);
    
    // Buat produk
    $product = Product::factory()->create();
    
    // Data update
    $updatedData = [
        'name' => 'Updated Product Name',
        'description' => $product->description,
        'price' => $product->price,
        'status' => $product->status,
        'category_id' => $product->category_id,
    ];
    
    // Submit update
    $response = $this->put(route('admin.products.update', $product), $updatedData);
    
    // Cek database
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product Name',
    ]);
    
    // Cek redirect
    $response->assertRedirect(route('admin.products.index'));
}); 
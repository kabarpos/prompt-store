<?php

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Inertia\Testing\AssertableInertia as Assert;

test('customer can view their orders', function () {
    // Buat user
    $user = User::factory()->create();
    
    // Buat beberapa order untuk user tersebut
    Order::factory()->count(3)->create(['user_id' => $user->id]);
    
    // Login sebagai user tersebut
    $this->actingAs($user);
    
    // Akses halaman orders
    $response = $this->get(route('customer.orders.index'));
    
    // Cek response
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Customer/Orders/Index')
            ->has('orders.data', 3)
        );
});

test('customer can view order detail', function () {
    // Buat user
    $user = User::factory()->create();
    
    // Buat order untuk user tersebut
    $order = Order::factory()->create(['user_id' => $user->id]);
    
    // Login sebagai user tersebut
    $this->actingAs($user);
    
    // Akses halaman detail order
    $response = $this->get(route('customer.orders.show', $order->id));
    
    // Cek response
    $response->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Customer/Orders/Show')
            ->has('order', fn (Assert $page) => $page
                ->where('id', $order->id)
                ->etc()
            )
        );
});

test('guest cannot access customer orders', function () {
    // Akses halaman orders tanpa login
    $response = $this->get(route('customer.orders.index'));
    
    // Seharusnya redirect ke login
    $response->assertRedirect(route('login'));
});

test('customer can create a new order', function () {
    // Buat user
    $user = User::factory()->create();
    
    // Buat beberapa produk
    $product = Product::factory()->create(['price' => 50000, 'stock' => 10]);
    
    // Login sebagai user
    $this->actingAs($user);
    
    // Data order baru
    $orderData = [
        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => $product->price
            ]
        ],
        'shipping_address' => 'Jalan Contoh No. 123',
        'shipping_method' => 'regular',
        'payment_method' => 'bank_transfer'
    ];
    
    // Submit order baru
    $response = $this->post(route('orders.store'), $orderData);
    
    // Cek database
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'status' => 'pending',
    ]);
    
    // Verifikasi produk terupdate stoknya
    $this->assertEquals(8, $product->fresh()->stock);
}); 
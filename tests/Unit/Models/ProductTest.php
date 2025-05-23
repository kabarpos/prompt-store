<?php

test('product has correct relationships', function () {
    $product = new \App\Models\Product();
    
    expect($product->category())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($product->gallery())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('product price attribute returns formatted value', function () {
    $product = \App\Models\Product::factory()->make([
        'price' => 1000000
    ]);
    
    expect($product->price_formatted)->toBe('Rp1.000.000');
});

test('product can be filtered by status', function () {
    \App\Models\Product::factory()->create(['status' => 'published']);
    \App\Models\Product::factory()->create(['status' => 'draft']);
    
    $publishedProducts = \App\Models\Product::whereStatus('published')->get();
    $draftProducts = \App\Models\Product::whereStatus('draft')->get();
    
    expect($publishedProducts)->toHaveCount(1)
        ->and($draftProducts)->toHaveCount(1);
}); 
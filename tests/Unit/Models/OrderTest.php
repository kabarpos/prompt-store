<?php

test('order has correct relationships', function () {
    $order = new \App\Models\Order();
    
    expect($order->user())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($order->items())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class)
        ->and($order->payment())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});

test('order status helpers return correct values', function () {
    $pendingOrder = \App\Models\Order::factory()->make(['status' => 'pending']);
    $completedOrder = \App\Models\Order::factory()->make(['status' => 'completed']);
    $cancelledOrder = \App\Models\Order::factory()->make(['status' => 'cancelled']);
    
    expect($pendingOrder->isPending())->toBeTrue()
        ->and($completedOrder->isCompleted())->toBeTrue()
        ->and($cancelledOrder->isCancelled())->toBeTrue();
});

test('order total is calculated correctly', function () {
    $order = \App\Models\Order::factory()->create(['subtotal' => 100000, 'discount' => 10000, 'tax' => 5000, 'shipping' => 15000]);
    
    expect($order->total)->toBe(110000); // 100000 - 10000 + 5000 + 15000
}); 
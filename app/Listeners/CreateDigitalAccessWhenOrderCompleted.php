<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use App\Models\DigitalAccess;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateDigitalAccessWhenOrderCompleted implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompletedEvent $event): void
    {
        $order = $event->order;
        
        // Hanya proses untuk order dengan status completed
        if ($order->status !== 'completed') {
            return;
        }
        
        // Load relasi yang dibutuhkan
        $order->load('items.product');
        
        foreach ($order->items as $item) {
            // Periksa apakah produk digital
            if (!$item->isDigitalProduct()) {
                continue;
            }
            
            $product = $item->product;
            
            // Tentukan tanggal kedaluwarsa jika ada
            $expiresAt = null;
            if ($product->access_days > 0) {
                $expiresAt = now()->addDays($product->access_days);
            }
            
            // Buat entri DigitalAccess
            $digitalAccess = DigitalAccess::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'product_id' => $product->id,
                'max_downloads' => $product->download_limit,
                'expires_at' => $expiresAt,
                'is_active' => true,
            ]);
        }
    }
} 
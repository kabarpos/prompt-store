<?php

namespace App\Listeners;

use App\Events\OrderCompletedEvent;
use App\Models\OrderDocument;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrderDocumentsWhenCompleted implements ShouldQueue
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

        foreach ($order->items as $item) {
            // Periksa apakah produk adalah produk digital
            if (!$item->product || !$item->product->is_digital) {
                continue;
            }

            // Hitung waktu kedaluwarsa dari access_days jika tersedia
            $expiresAt = null;
            if ($item->product->access_days) {
                $expiresAt = now()->addDays($item->product->access_days);
            }

            // Buat dokumen unduhan untuk produk digital
            $document = OrderDocument::create([
                'order_id' => $order->id,
                'type' => OrderDocument::TYPE_DOWNLOAD,
                'title' => 'Akses Digital: ' . $item->product->name,
                'content' => 'Anda dapat mengakses produk digital Anda. Silakan klik tombol download untuk mengunduh.',
                'file_path' => $item->product->digital_file_path,
                'expires_at' => $expiresAt,
                'is_sent' => false,
            ]);
        }
    }
} 
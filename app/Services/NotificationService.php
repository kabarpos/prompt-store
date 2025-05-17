<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDocument;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderDocumentMail;

class NotificationService
{
    /**
     * Kirim notifikasi kredensial login
     *
     * @param OrderDocument $document
     * @return bool
     */
    public function sendCredentials(OrderDocument $document): bool
    {
        return $this->sendDocumentNotification($document);
    }
    
    /**
     * Kirim notifikasi informasi domain
     *
     * @param OrderDocument $document
     * @return bool
     */
    public function sendDomainInfo(OrderDocument $document): bool
    {
        return $this->sendDocumentNotification($document);
    }
    
    /**
     * Kirim notifikasi update
     *
     * @param OrderDocument $document
     * @return bool
     */
    public function sendUpdateInfo(OrderDocument $document): bool
    {
        return $this->sendDocumentNotification($document);
    }
    
    /**
     * Kirim notifikasi download
     *
     * @param OrderDocument $document
     * @return bool
     */
    public function sendDownloadLink(OrderDocument $document): bool
    {
        return $this->sendDocumentNotification($document);
    }
    
    /**
     * Kirim notifikasi dokumen
     *
     * @param OrderDocument $document
     * @return bool
     */
    protected function sendDocumentNotification(OrderDocument $document): bool
    {
        try {
            // Update status dokumen terlebih dahulu sebelum mengirim email
            // untuk memastikan status terupdate meskipun pengiriman email gagal
            $document->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
            
            // Log status dokumen setelah update
            \Log::info('Status dokumen diperbarui sebelum kirim email', [
                'document_id' => $document->id,
                'is_sent' => $document->is_sent,
                'sent_at' => $document->sent_at
            ]);
            
            $order = $document->order;
            $emailTo = $order->user_id ? $order->user->email : $order->customer_email;
            
            // Kirim email
            Mail::to($emailTo)->send(new OrderDocumentMail($document));
            
            // Memastikan lagi status dokumen terupdate dan tersimpan ke database
            $document->refresh();
            if (!$document->is_sent) {
                $document->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);
                $document->refresh();
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error sending document notification: ' . $e->getMessage());
            return false;
        }
    }
} 
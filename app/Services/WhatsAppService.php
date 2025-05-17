<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\WebsiteSetting;
use App\Models\AdminWhatsapp;
use App\Models\WhatsAppTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiKey;
    protected $webhookUrl;
    protected $isActive;
    protected $senderPhone;

    public function __construct()
    {
        $settings = WebsiteSetting::first();
        
        if (!$settings) {
            Log::error('WhatsApp configuration missing: Website settings not found');
            throw new \Exception('Konfigurasi WhatsApp belum diatur. Silakan atur di halaman pengaturan admin.');
        }
        
        // Prioritaskan penggunaan konfigurasi dari database
        $this->apiKey = $settings->webhook_api_key;
        $this->webhookUrl = $settings->webhook_url;
        $this->senderPhone = $settings->webhook_sender_phone;
        $this->isActive = $settings->webhook_is_active ?? false;

        if (!$this->apiKey || !$this->webhookUrl) {
            Log::error('WhatsApp configuration missing: API key or webhook URL not set');
            throw new \Exception('API key atau webhook URL WhatsApp belum diatur. Silakan atur di halaman pengaturan admin.');
        }

        if (!$this->senderPhone) {
            Log::error('WhatsApp configuration missing: Sender phone number not set');
            throw new \Exception('Nomor WhatsApp pengirim belum diatur. Silakan atur di halaman pengaturan admin.');
        }

        // Log konfigurasi untuk debugging
        Log::info('WhatsApp Service Configuration', [
            'api_key_set' => !empty($this->apiKey),
            'webhook_url_set' => !empty($this->webhookUrl),
            'sender_phone_set' => !empty($this->senderPhone),
            'is_active' => $this->isActive
        ]);
    }

    /**
     * Send a WhatsApp message
     * 
     * @param string $phone
     * @param string $message
     * @param array $variables
     * @return bool
     */
    public function sendMessage($phone, $message, $variables = [])
    {
        if (!$this->isActive) {
            Log::warning('WhatsApp notification disabled. Please enable it in admin settings.');
            throw new \Exception('WhatsApp notification tidak aktif. Silakan aktifkan di pengaturan admin.');
        }

        if (!$this->apiKey || !$this->webhookUrl || !$this->senderPhone) {
            Log::error('WhatsApp configuration missing', [
                'api_key_exists' => !empty($this->apiKey),
                'webhook_url_exists' => !empty($this->webhookUrl),
                'sender_phone_exists' => !empty($this->senderPhone)
            ]);
            throw new \Exception('Konfigurasi WhatsApp tidak lengkap. Silakan periksa API key, webhook URL, dan nomor pengirim.');
        }

        try {
            // Format nomor telepon
            $phone = $this->formatPhoneNumber($phone);
            $senderPhone = $this->formatPhoneNumber($this->senderPhone);

            // Validasi format nomor
            if (!preg_match('/^62[0-9]{9,}$/', $phone)) {
                throw new \Exception('Format nomor tujuan tidak valid: ' . $phone);
            }
            if (!preg_match('/^62[0-9]{9,}$/', $senderPhone)) {
                throw new \Exception('Format nomor pengirim tidak valid: ' . $senderPhone);
            }

            // Replace variables in message if any
            if (!empty($variables)) {
                $message = $this->replaceVariables($message, $variables);
            }

            // Log request
            Log::info('Preparing to send WhatsApp message', [
                'from' => $senderPhone,
                'to' => $phone,
                'message' => $message,
                'variables' => $variables
            ]);

            // Format request sesuai dengan API Dripsender
            $payload = [
                'api_key' => $this->apiKey,
                'phone' => $phone,
                'text' => $message
            ];

            // Log request detail sebelum mengirim
            Log::info('Preparing WhatsApp request', [
                'url' => 'https://api.dripsender.id/send',
                'payload' => array_merge($payload, ['api_key' => '***']),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ]);

            // Kirim pesan via API
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->post('https://api.dripsender.id/send', $payload);

            // Log response detail
            Log::info('WhatsApp API Response', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->json() ?? $response->body(),
                'request_url' => 'https://api.dripsender.id/send',
                'request_payload' => array_merge($payload, ['api_key' => '***'])
            ]);

            // Cek response
            if (!$response->successful()) {
                $errorMessage = $response->json()['message'] ?? $response->body();
                throw new \Exception('Gagal mengirim pesan: ' . $errorMessage);
            }

            $responseData = $response->json();
            if (isset($responseData['success']) && !$responseData['success']) {
                throw new \Exception('Gagal mengirim pesan: ' . ($responseData['message'] ?? 'Unknown error'));
            }

            Log::info('WhatsApp message sent successfully', [
                'to' => $phone,
                'response' => $responseData
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message', [
                'from' => $this->senderPhone,
                'to' => $phone,
                'message' => $message,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Format phone number to match WhatsApp requirements
     */
    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure number starts with 62
        if (substr($phone, 0, 2) !== '62') {
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } else if (substr($phone, 0, 1) === '8') {
                $phone = '62' . $phone;
            }
        }
        
        Log::info('Formatted phone number', [
            'original' => $phone,
            'formatted' => $phone
        ]);
        
        return $phone;
    }

    /**
     * Format parameter template WhatsApp
     * 
     * @param array $variables
     * @return array
     */
    protected function formatTemplateParameters($variables)
    {
        $parameters = [];
        foreach ($variables as $key => $value) {
            $parameters[] = [
                'type' => 'text',
                'text' => $value
            ];
        }
        return $parameters;
    }

    /**
     * Kirim notifikasi perubahan status order
     * 
     * @param \App\Models\Order $order
     * @param string $oldStatus
     * @return bool
     */
    public function sendOrderStatusChangedNotification($order, $oldStatus)
    {
        // Cari template yang sesuai dengan status baru
        $template = \App\Models\WhatsAppTemplate::where('trigger_status', $order->status)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            Log::warning('Template WhatsApp tidak ditemukan untuk status: ' . $order->status);
            return false;
        }

        // Siapkan variabel untuk template
        $variables = [
            'order_number' => $order->order_number,
            'order_date' => $order->created_at->format('d/m/Y H:i'),
            'customer_name' => $order->user->name,
            'total_amount' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'items_list' => $this->formatOrderItems($order->items),
            'subtotal' => 'Rp ' . number_format($order->subtotal, 0, ',', '.'),
            'admin_fee' => 'Rp ' . number_format($order->admin_fee, 0, ',', '.'),
            'discount' => 'Rp ' . number_format($order->discount, 0, ',', '.'),
        ];

        // Log variabel untuk debugging
        Log::info('Variables for WhatsApp message', [
            'order_id' => $order->id,
            'variables' => $variables
        ]);

        // Kirim ke customer
        if ($template->type === 'customer' && $order->user->whatsapp) {
            $this->sendMessage(
                $order->user->whatsapp,
                $template->message_template,
                $variables
            );
        }

        // Kirim ke admin
        if ($template->type === 'admin') {
            $adminNumbers = \App\Models\AdminWhatsapp::where('is_active', true)
                ->pluck('phone_number');

            foreach ($adminNumbers as $number) {
                $this->sendMessage(
                    $number,
                    $template->message_template,
                    $variables
                );
            }
        }

        return true;
    }

    /**
     * Kirim notifikasi saat order baru dibuat
     * 
     * @param \App\Models\Order $order
     * @return bool
     */
    public function sendOrderCreatedNotification(Order $order)
    {
        if (!$this->isActive) {
            Log::info('WhatsApp notification disabled: Order created notification not sent');
            return false;
        }

        // Cari template untuk customer
        $customerTemplate = WhatsAppTemplate::where('type', 'customer')
            ->where('trigger_status', 'new_order')
            ->where('is_active', true)
            ->first();

        // Cari template untuk admin
        $adminTemplate = WhatsAppTemplate::where('type', 'admin')
            ->where('trigger_status', 'new_order')
            ->where('is_active', true)
            ->first();

        // Siapkan variabel untuk template
        $variables = [
            'order_number' => $order->order_number,
            'order_date' => $order->created_at->format('d/m/Y H:i'),
            'customer_name' => $order->user->name,
            'total_amount' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'items_list' => $this->formatOrderItems($order->items),
            'subtotal' => 'Rp ' . number_format($order->subtotal, 0, ',', '.'),
            'admin_fee' => 'Rp ' . number_format($order->admin_fee, 0, ',', '.'),
            'discount' => 'Rp ' . number_format($order->discount, 0, ',', '.'),
        ];

        $success = true;

        // Kirim ke customer jika ada template dan nomor WhatsApp
        if ($customerTemplate && $order->user->whatsapp) {
            try {
                $this->sendMessage(
                    $order->user->whatsapp,
                    $customerTemplate->message_template,
                    $variables
                );
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi WhatsApp ke customer', [
                    'order_id' => $order->id,
                    'customer_phone' => $order->user->whatsapp,
                    'error' => $e->getMessage()
                ]);
                $success = false;
            }
        } else {
            Log::info('Tidak mengirim notifikasi ke customer: Template tidak aktif atau nomor tidak tersedia', [
                'has_template' => !is_null($customerTemplate),
                'has_phone' => !empty($order->user->whatsapp)
            ]);
        }

        // Kirim ke admin jika ada template
        if ($adminTemplate) {
            $adminNumbers = AdminWhatsapp::where('is_active', true)
                ->pluck('phone_number');

            if ($adminNumbers->isEmpty()) {
                Log::warning('Tidak ada nomor WhatsApp admin yang aktif');
            } else {
                foreach ($adminNumbers as $number) {
                    try {
                        $this->sendMessage(
                            $number,
                            $adminTemplate->message_template,
                            $variables
                        );
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim notifikasi WhatsApp ke admin', [
                            'order_id' => $order->id,
                            'admin_phone' => $number,
                            'error' => $e->getMessage()
                        ]);
                        $success = false;
                    }
                }
            }
        } else {
            Log::info('Tidak mengirim notifikasi ke admin: Template tidak aktif');
        }

        return $success;
    }

    /**
     * Replace variabel di template
     * 
     * @param string $template
     * @param array $variables
     * @return string
     */
    protected function replaceVariables($template, $variables)
    {
        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
            // Tambahan untuk format lain
            $template = str_replace('{{' . $key . '}}', $value, $template);
            $template = str_replace('{'.$key.'}', $value, $template);
        }
        
        return $template;
    }

    /**
     * Format daftar item order
     * 
     * @param \Illuminate\Database\Eloquent\Collection $items
     * @return string
     */
    protected function formatOrderItems($items)
    {
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = sprintf(
                "- %dx %s: Rp %s",
                $item->quantity,
                $item->product->name,
                number_format($item->subtotal, 0, ',', '.')
            );
        }
        return implode("\n", $formattedItems);
    }

    /**
     * Validasi konfigurasi webhook
     * 
     * @return bool
     */
    public function validateWebhook()
    {
        if (!$this->isActive || !$this->apiKey || !$this->webhookUrl) {
            Log::warning('Konfigurasi webhook tidak lengkap');
            return false;
        }

        try {
            // Pastikan URL valid
            if (!filter_var($this->webhookUrl, FILTER_VALIDATE_URL)) {
                Log::error('URL webhook tidak valid');
                return false;
            }

            // Cek apakah URL dapat diakses
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get($this->webhookUrl . '/status');

            if (!$response->successful()) {
                Log::error('Validasi webhook gagal', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error saat validasi webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
} 
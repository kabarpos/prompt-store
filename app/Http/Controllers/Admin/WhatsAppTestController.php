<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class WhatsAppTestController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Menampilkan halaman test WhatsApp
     */
    public function index()
    {
        $settings = WebsiteSetting::first();
        $logs = WhatsAppLog::where('type', '=', 'test')
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('admin/notifications/TestWhatsApp', [
            'settings' => [
                'is_active' => $settings->webhook_is_active ?? false,
                'api_key_set' => !empty($settings->webhook_api_key),
                'webhook_url' => $settings->webhook_url,
                'sender_phone' => $settings->webhook_sender_phone,
            ],
            'logs' => $logs
        ]);
    }

    /**
     * Mengirim pesan test WhatsApp
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^62[0-9]{9,}$/',
            'message' => 'required|string'
        ], [
            'phone.required' => 'Nomor WhatsApp harus diisi',
            'phone.regex' => 'Format nomor WhatsApp tidak valid (harus diawali 62)',
            'message.required' => 'Pesan harus diisi'
        ]);

        try {
            Log::info('Mencoba mengirim pesan test', [
                'phone' => $request->phone,
                'message' => $request->message
            ]);

            $result = $this->whatsappService->sendMessage(
                $request->phone,
                $request->message
            );

            if (!$result) {
                throw new \Exception('Gagal mengirim pesan WhatsApp');
            }

            // Simpan log pengiriman
            WhatsAppLog::create([
                'phone' => $request->phone,
                'message' => $request->message,
                'type' => 'test',
                'status' => 'success',
                'template_id' => null // Explicitly set to null for test messages
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan test berhasil dikirim'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saat test kirim pesan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Simpan log error
            WhatsAppLog::create([
                'phone' => $request->phone,
                'message' => $request->message,
                'type' => 'test',
                'status' => 'failed',
                'error' => $e->getMessage(),
                'template_id' => null // Explicitly set to null for test messages
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan test: ' . $e->getMessage()
            ], 500);
        }
    }
} 
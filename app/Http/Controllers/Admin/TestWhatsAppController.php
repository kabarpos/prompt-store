<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestWhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function testSend(Request $request)
    {
        try {
            $phone = $request->input('phone');
            $message = $request->input('message', 'Test pesan WhatsApp');

            Log::info('Mencoba mengirim pesan test', [
                'phone' => $phone,
                'message' => $message
            ]);

            $result = $this->whatsappService->sendMessage($phone, $message);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error saat test kirim pesan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 
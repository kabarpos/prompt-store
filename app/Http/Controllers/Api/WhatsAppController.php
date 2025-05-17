<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppController extends Controller
{
    /**
     * Get the next WhatsApp number in rotation
     */
    public function getNext()
    {
        try {
            $whatsapp = AdminWhatsapp::getNextInRotation();
            
            Log::info('WhatsApp Rotation', [
                'whatsapp' => $whatsapp ? $whatsapp->toArray() : null
            ]);
            
            if (!$whatsapp || !$whatsapp->phone_number) {
                Log::warning('No valid WhatsApp number found', [
                    'whatsapp' => $whatsapp ? $whatsapp->toArray() : null
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada nomor WhatsApp yang tersedia'
                ], 404);
            }
            
            // Update last_used_at
            $whatsapp->markAsUsed();
            
            return response()->json([
                'success' => true,
                'phone_number' => $whatsapp->phone_number
            ]);
        } catch (Exception $e) {
            Log::error('WhatsApp API Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil nomor WhatsApp'
            ], 500);
        }
    }
} 
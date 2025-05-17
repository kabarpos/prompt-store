<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class WhatsAppTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $customerTemplates = WhatsAppTemplate::where('type', WhatsAppTemplate::TYPE_CUSTOMER)
            ->orderBy('order')
            ->get();
            
        $adminTemplates = WhatsAppTemplate::where('type', WhatsAppTemplate::TYPE_ADMIN)
            ->orderBy('order')
            ->get();
            
        return Inertia::render('admin/notifications/Index', [
            'customerTemplates' => $customerTemplates,
            'adminTemplates' => $adminTemplates,
            'availableVariables' => WhatsAppTemplate::getAvailableVariables(),
            'triggerStatusOptions' => $this->getTriggerStatusOptions(),
        ]);
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return Inertia::render('admin/notifications/Create', [
            'availableVariables' => WhatsAppTemplate::getAvailableVariables(),
            'triggerStatusOptions' => $this->getTriggerStatusOptions(),
        ]);
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:customer,admin',
            'trigger_status' => 'required|string',
            'message_template' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);
        
        WhatsAppTemplate::create($validated);
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Template berhasil dibuat');
    }

    /**
     * Display the specified template.
     */
    public function show(WhatsAppTemplate $template)
    {
        // Ambil log pengiriman 24 jam terakhir
        $logs = \App\Models\WhatsAppLog::where('created_at', '>=', now()->subDay())
            ->where('message', 'LIKE', '%' . $template->message_template . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('admin/notifications/Show', [
            'template' => $template,
            'availableVariables' => WhatsAppTemplate::getAvailableVariables(),
            'triggerStatusOptions' => $this->getTriggerStatusOptions(),
            'sendingLogs' => $logs
        ]);
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(WhatsAppTemplate $template)
    {
        return Inertia::render('admin/notifications/Edit', [
            'template' => $template,
            'availableVariables' => WhatsAppTemplate::getAvailableVariables(),
            'triggerStatusOptions' => $this->getTriggerStatusOptions(),
        ]);
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, WhatsAppTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message_template' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);
        
        $template->update($validated);
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Template berhasil diperbarui');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(WhatsAppTemplate $template)
    {
        $template->delete();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Template berhasil dihapus');
    }
    
    /**
     * Toggle the active status of the specified template.
     */
    public function toggleActive(WhatsAppTemplate $template)
    {
        $template->update([
            'is_active' => !$template->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'Status template berhasil diperbarui');
    }
    
    /**
     * Get options for trigger status dropdown.
     */
    private function getTriggerStatusOptions()
    {
        return [
            'new_order' => 'Order Baru',
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Sedang Diproses',
            'review' => 'Menunggu Review',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'payment_confirmed' => 'Pembayaran Dikonfirmasi',
        ];
    }

    /**
     * Test pengiriman template WhatsApp
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WhatsAppTemplate  $template
     * @return \Illuminate\Http\JsonResponse
     */
    public function testSend(Request $request, WhatsAppTemplate $template)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^62[0-9]{9,}$/'
        ], [
            'phone.required' => 'Nomor WhatsApp harus diisi',
            'phone.regex' => 'Format nomor WhatsApp tidak valid (harus diawali 62)'
        ]);

        try {
            // Log request
            Log::info('Mengirim test WhatsApp template', [
                'template_id' => $template->id,
                'template_name' => $template->name,
                'phone' => $request->phone
            ]);

            // Siapkan contoh variabel
            $exampleVariables = [
                'order_number' => 'ORD' . date('Ymd') . rand(1000, 9999),
                'order_date' => now()->format('d/m/Y H:i'),
                'customer_name' => 'User Test',
                'total_amount' => 'Rp ' . number_format(rand(100000, 1000000), 0, ',', '.'),
                'payment_method' => 'Bank Transfer',
                'status' => 'Sedang Diproses',
                'items_list' => "- 1x Produk Digital A: Rp 150.000\n- 2x Produk Digital B: Rp 200.000",
                'subtotal' => 'Rp 350.000',
                'admin_fee' => 'Rp 0',
                'discount' => 'Rp 0',
            ];

            // Format pesan dengan variabel contoh
            $message = $template->message_template;
            foreach ($exampleVariables as $key => $value) {
                $message = str_replace("{{$key}}", $value, $message);
            }

            // Kirim pesan test
            $whatsapp = app(WhatsAppService::class);
            
            try {
                $result = $whatsapp->sendMessage(
                    $request->phone,
                    $message
                );

                if (!$result) {
                    throw new \Exception('Gagal mengirim pesan WhatsApp');
                }

                // Log success
                Log::info('Pesan test berhasil dikirim', [
                    'template_id' => $template->id,
                    'phone' => $request->phone,
                    'message' => $message
                ]);

                // Simpan log pengiriman
                \App\Models\WhatsAppLog::create([
                    'phone' => $request->phone,
                    'message' => $message,
                    'type' => 'test',
                    'status' => 'success'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesan test berhasil dikirim'
                ]);

            } catch (\Exception $e) {
                Log::error('Error saat mengirim pesan WhatsApp', [
                    'template_id' => $template->id,
                    'phone' => $request->phone,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Simpan log error
                \App\Models\WhatsAppLog::create([
                    'phone' => $request->phone,
                    'message' => $message ?? $template->message_template,
                    'type' => 'test',
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ]);

                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan test: ' . $e->getMessage()
            ], 500);
        }
    }
}

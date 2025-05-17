<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Update webhook settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateWebhook(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'nullable|string',
            'webhook_url' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Log untuk debug
        \Log::info('Webhook update request', [
            'api_key_set' => !empty($validated['api_key']),
            'webhook_url' => $validated['webhook_url'],
            'is_active' => $validated['is_active'] ?? false
        ]);

        // Dapatkan instance settings
        $settings = WebsiteSetting::first();
        
        if (!$settings) {
            $settings = WebsiteSetting::create([
                'site_name' => 'Website Name',
                'webhook_api_key' => $validated['api_key'] ?? null,
                'webhook_url' => $validated['webhook_url'] ?? null,
                'webhook_is_active' => $validated['is_active'] ?? false,
            ]);
        } else {
            // Update hanya kolom webhook
            $settings->update([
                'webhook_api_key' => $validated['api_key'] ?? null,
                'webhook_url' => $validated['webhook_url'] ?? null,
                'webhook_is_active' => $validated['is_active'] ?? false,
            ]);
        }

        // Log hasil update
        \Log::info('Webhook settings updated', [
            'settings_id' => $settings->id,
            'api_key_set' => !empty($settings->webhook_api_key),
            'webhook_url' => $settings->webhook_url,
            'is_active' => $settings->webhook_is_active
        ]);

        // Coba validasi webhook jika aktif
        if ($settings->webhook_is_active) {
            try {
                $whatsapp = app(WhatsAppService::class);
                $isValid = $whatsapp->validateWebhook();
                
                \Log::info('Webhook validation result', [
                    'is_valid' => $isValid
                ]);
                
                if (!$isValid) {
                    return redirect()->back()
                        ->with('error', 'Webhook tidak dapat divalidasi. Mohon periksa konfigurasi.');
                }
            } catch (\Exception $e) {
                \Log::error('Webhook validation error', [
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan saat validasi webhook: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', 'Pengaturan webhook berhasil diperbarui');
    }
    
    /**
     * Update logo for the website.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Dapatkan instance settings
        $settings = WebsiteSetting::getSettings();
        
        // Hapus logo lama jika ada
        if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }
        
        // Simpan logo baru
        $path = $request->file('logo')->store('logos', 'public');
        
        // Update settings
        $settings->update([
            'logo_path' => $path,
        ]);
        
        // Hapus cache website settings
        $this->clearWebsiteSettingsCache();
        
        \Log::info("Logo berhasil diperbarui. Path: {$path}");
        
        return response()->json([
            'success' => true,
            'message' => 'Logo berhasil diperbarui',
            'path' => $path,
            'url' => $settings->getLogoUrl(),
        ]);
    }
    
    /**
     * Clear website settings cache.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCache()
    {
        $this->clearWebsiteSettingsCache();
        
        return response()->json([
            'success' => true,
            'message' => 'Cache website settings berhasil dihapus',
        ]);
    }
    
    /**
     * Helper method to clear website settings cache.
     */
    private function clearWebsiteSettingsCache()
    {
        Cache::forget('website_settings');
        \Log::info('Website settings cache cleared');
    }
} 
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Menampilkan halaman pengaturan website.
     */
    public function index()
    {
        $settings = WebsiteSetting::getSettings();
        
        // Ambil data WhatsApp Admin dan Social Media
        $whatsappAdmins = \App\Models\AdminWhatsapp::orderBy('order')->get();
        $socialMedia = \App\Models\SocialMedia::orderBy('order')->get();
        
        return Inertia::render('admin/settings/Index', [
            'settings' => $settings,
            'mediaUrls' => [
                'logo' => $settings->getLogoUrl(),
                'favicon' => $settings->getFaviconUrl(),
                'og_image' => $settings->getOgImageUrl(),
            ],
            'whatsappAdmins' => $whatsappAdmins,
            'socialMedia' => $socialMedia,
            'title' => 'Pengaturan Website',
        ]);
    }

    /**
     * Menyimpan pengaturan umum website.
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_subtitle' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'homepage_route' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string',
        ]);

        $settings = WebsiteSetting::getSettings();
        $settings->update($validated);

        return redirect()->back()
            ->with('message', 'Pengaturan umum berhasil disimpan');
    }

    /**
     * Menyimpan pengaturan footer website.
     */
    public function updateFooter(Request $request)
    {
        $validated = $request->validate([
            'copyright_text' => 'nullable|string|max:255',
            'copyright_year' => 'nullable|integer|min:2000|max:2100',
        ]);

        $settings = WebsiteSetting::getSettings();
        $settings->update($validated);

        return redirect()->back()
            ->with('message', 'Pengaturan footer berhasil disimpan');
    }

    /**
     * Menyimpan pengaturan skrip tracking.
     */
    public function updateScripts(Request $request)
    {
        $validated = $request->validate([
            'header_scripts' => 'nullable|string',
            'meta_pixel_script' => 'nullable|string',
            'tiktok_pixel_script' => 'nullable|string',
            'google_tag_script' => 'nullable|string',
            'footer_scripts' => 'nullable|string',
        ]);

        $settings = WebsiteSetting::getSettings();
        $settings->update($validated);

        return redirect()->back()
            ->with('message', 'Pengaturan skrip berhasil disimpan');
    }

    /**
     * Upload dan simpan logo website.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $settings = WebsiteSetting::getSettings();
        
        // Hapus logo lama jika ada
        if ($settings->logo_path && Storage::disk('public')->exists($settings->logo_path)) {
            Storage::disk('public')->delete($settings->logo_path);
        }
        
        // Simpan logo baru
        $logoPath = $request->file('logo')->store('website', 'public');
        $settings->update(['logo_path' => $logoPath]);

        return redirect()->back()
            ->with('message', 'Logo berhasil diupload');
    }

    /**
     * Upload dan simpan favicon website.
     */
    public function uploadFavicon(Request $request)
    {
        $request->validate([
            'favicon' => 'required|image|mimes:ico,png|max:1024',
        ]);

        $settings = WebsiteSetting::getSettings();
        
        // Hapus favicon lama jika ada
        if ($settings->favicon_path && Storage::disk('public')->exists($settings->favicon_path)) {
            Storage::disk('public')->delete($settings->favicon_path);
        }
        
        // Simpan favicon baru
        $faviconPath = $request->file('favicon')->store('website', 'public');
        $settings->update(['favicon_path' => $faviconPath]);

        return redirect()->back()
            ->with('message', 'Favicon berhasil diupload');
    }

    /**
     * Upload dan simpan gambar OG default.
     */
    public function uploadOgImage(Request $request)
    {
        $request->validate([
            'og_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $settings = WebsiteSetting::getSettings();
        
        // Hapus gambar OG lama jika ada
        if ($settings->default_og_image_path && Storage::disk('public')->exists($settings->default_og_image_path)) {
            Storage::disk('public')->delete($settings->default_og_image_path);
        }
        
        // Simpan gambar OG baru
        $ogImagePath = $request->file('og_image')->store('website', 'public');
        $settings->update(['default_og_image_path' => $ogImagePath]);

        return redirect()->back()
            ->with('message', 'Gambar OG berhasil diupload');
    }

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
            'sender_phone' => 'nullable|string|regex:/^[0-9]+$/|min:10|max:15',
            'is_active' => 'boolean',
        ]);

        // Log untuk debug
        \Log::info('Webhook update request', [
            'is_active_raw' => $request->input('is_active'),
            'is_active_validated' => $validated['is_active'] ?? 'not set',
            'all_data' => $request->all()
        ]);

        // Dapatkan instance settings
        $settings = WebsiteSetting::first();
        
        // Pastikan nilai is_active didapat dengan benar
        $isActive = isset($validated['is_active']) ? (bool)$validated['is_active'] : true;
        
        // Log nilai setelah konversi
        \Log::info('Webhook is_active after conversion', ['is_active' => $isActive]);
        
        if (!$settings) {
            $settings = WebsiteSetting::create([
                'site_name' => 'Website Name',
                'webhook_api_key' => $validated['api_key'] ?? null,
                'webhook_url' => $validated['webhook_url'] ?? null,
                'webhook_sender_phone' => $validated['sender_phone'] ?? null,
                'webhook_is_active' => $isActive,
            ]);
        } else {
            // Update hanya kolom webhook
            $settings->update([
                'webhook_api_key' => $validated['api_key'] ?? null,
                'webhook_url' => $validated['webhook_url'] ?? null,
                'webhook_sender_phone' => $validated['sender_phone'] ?? null,
                'webhook_is_active' => $isActive,
            ]);
        }

        // Refresh model dari database untuk memastikan nilai terupdate pada respons
        $settings->refresh();
        
        // Log nilai pada model setelah disimpan
        \Log::info('Webhook settings after save', [
            'webhook_is_active' => $settings->webhook_is_active,
            'is_webhook_is_active_boolean' => is_bool($settings->webhook_is_active),
            'webhook_sender_phone' => $settings->webhook_sender_phone
        ]);

        return redirect()->back()->with('success', 'Pengaturan webhook berhasil diperbarui');
    }
}

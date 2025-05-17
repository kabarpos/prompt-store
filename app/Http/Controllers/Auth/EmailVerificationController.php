<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomVerifyEmail;
use App\Models\EmailSetting;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Tampilkan halaman notifikasi verifikasi email
     */
    public function notice(Request $request): Response|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        return Inertia::render('auth/VerifyEmail');
    }

    /**
     * Kirim ulang email verifikasi
     */
    public function send(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        // Periksa pengaturan untuk melihat apakah verifikasi diaktifkan
        $settings = EmailSetting::getSettings();
        if (!$settings->enable_verification) {
            // Jika fitur verifikasi dinonaktifkan, verifikasi pengguna secara otomatis
            $request->user()->markEmailAsVerified();
            return redirect()->intended(route('dashboard'));
        }

        // Buat URL verifikasi
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $request->user()->getKey(), 'hash' => sha1($request->user()->getEmailForVerification())]
        );

        // Kirim email verifikasi kustom
        Mail::to($request->user()->email)
            ->send(new CustomVerifyEmail($request->user(), $verificationUrl));

        return back()->with('status', 'verification-link-sent');
    }

    /**
     * Verifikasi email
     */
    public function verify(Request $request): RedirectResponse
    {
        // Cari user berdasarkan ID yang diberikan pada URL
        $user = User::findOrFail($request->route('id'));
        
        // Jika email sudah diverifikasi, redirect saja
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard').'?verified=1');
        }

        // Verifikasi hash
        $hash = sha1($user->getEmailForVerification());
        if (!hash_equals($hash, (string) $request->route('hash'))) {
            abort(403, 'URL verifikasi tidak valid');
        }

        // Tandai email sebagai terverifikasi
        $user->markEmailAsVerified();
        
        // Aktifkan akun user jika statusnya masih inactive
        if ($user->status === 'inactive') {
            $user->update(['status' => 'active']);
            
            // Log informasi aktivasi
            \Log::info('User diaktifkan setelah verifikasi email dari link verifikasi', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
        
        // Dispatch event Verified
        event(new Verified($user));

        return redirect()->intended(route('dashboard').'?verified=1')
            ->with('verified', true)
            ->with('message', 'Email berhasil diverifikasi dan akun Anda telah diaktifkan.');
    }
}

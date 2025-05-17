<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\CustomVerifyEmail;
use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:'.User::class,
                function ($attribute, $value, $fail) {
                    // Daftar domain yang diblokir (contoh: domain disposable email)
                    $blockedDomains = [
                        'tempmail.com',
                        'throwawaymail.com',
                        'mailinator.com',
                        'guerrillamail.com',
                        '10minutemail.com',
                        'yopmail.com',
                        'temp-mail.org',
                        'fakeinbox.com',
                        'tempmail.net',
                        'dispostable.com',
                    ];
                    
                    // Daftar domain yang diperbolehkan (opsional, bisa dikosongkan untuk mengizinkan semua domain)
                    $allowedDomains = [
                        // Contoh domain yang diperbolehkan
                        // 'gmail.com',
                        // 'yahoo.com',
                        // 'hotmail.com',
                        // 'outlook.com',
                    ];
                    
                    $domain = substr(strrchr($value, "@"), 1);
                    
                    // Cek domain yang diblokir
                    if (in_array($domain, $blockedDomains)) {
                        $fail('Domain email ini tidak diperbolehkan untuk pendaftaran.');
                        return;
                    }
                    
                    // Jika ada daftar domain yang diperbolehkan, cek apakah domain ada dalam daftar
                    if (!empty($allowedDomains) && !in_array($domain, $allowedDomains)) {
                        $fail('Domain email tidak diperbolehkan untuk pendaftaran.');
                        return;
                    }
                    
                    // Validasi MX record hanya jika domain tidak dalam daftar yang diperbolehkan
                    if (empty($allowedDomains)) {
                        try {
                            if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                                $fail('Domain email tidak valid atau tidak memiliki server email.');
                            }
                        } catch (\Exception $e) {
                            // Jika terjadi error saat pengecekan DNS, kita abaikan
                            // Ini untuk menghindari masalah jika server DNS tidak merespons
                            \Log::warning('Gagal memeriksa DNS record untuk domain: ' . $domain);
                        }
                    }
                },
            ],
            'whatsapp' => 'nullable|string|max:20|regex:/^\+?[0-9\s\-\(\)]+$/',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp' => $request->whatsapp,
            'password' => Hash::make($request->password),
            'status' => 'inactive',
        ]);

        // Cari role 'user' dan berikan ke user baru
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->assignRole($userRole);
        }

        // PENTING: TIDAK memanggil event Registered karena akan memicu pengiriman email bawaan Laravel
        // event(new Registered($user));

        // Periksa apakah verifikasi email diaktifkan
        $emailSettings = EmailSetting::getSettings();
        if ($emailSettings->enable_verification) {
            // Buat URL verifikasi
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
            );

            // Kirim email verifikasi kustom
            Mail::to($user->email)
                ->send(new CustomVerifyEmail($user, $verificationUrl));

            \Illuminate\Support\Facades\Log::info('Email verifikasi kustom dikirim saat pendaftaran', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect()->route('login')
                ->with('status', 'Pendaftaran berhasil! Silakan verifikasi email Anda sebelum login.');
        }

        // Tidak melakukan login otomatis, karena perlu persetujuan admin
        // Auth::login($user);

        return redirect()->route('login')
            ->with('status', 'Pendaftaran berhasil! Silakan tunggu persetujuan admin untuk dapat login.');
    }
}

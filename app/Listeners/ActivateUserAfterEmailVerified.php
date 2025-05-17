<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ActivateUserAfterEmailVerified
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;
        
        // Aktifkan user jika statusnya inactive
        if ($user && $user->status === 'inactive') {
            $user->update(['status' => 'active']);
            
            Log::info('User diaktifkan setelah verifikasi email', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
    }
} 
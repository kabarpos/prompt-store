<?php

namespace App\Mail;

use App\Models\EmailSetting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * User model
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Reset URL
     *
     * @var string
     */
    protected $resetUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $resetUrl)
    {
        $this->user = $user;
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $settings = EmailSetting::getSettings();
        
        if ($settings && $settings->enable_reset_password_template && $settings->reset_password_template) {
            // Gunakan template kustom dari pengaturan
            $content = $settings->reset_password_template;
            $content = str_replace('{name}', $this->user->name, $content);
            $content = str_replace('{reset_url}', $this->resetUrl, $content);
            
            return new Content(
                htmlString: $content,
            );
        }
        
        // Fallback ke template default
        return new Content(
            markdown: 'emails.reset-password',
            with: [
                'user' => $this->user,
                'url' => $this->resetUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 
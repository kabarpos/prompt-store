<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public $token;
    public $template;
    public $user;

    public function __construct($token, $template, $user)
    {
        $this->token = $token;
        $this->template = $template;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $html = str_replace(
            ['{name}', '{reset_url}'],
            [$this->user->name, $resetUrl],
            $this->template
        );

        return (new MailMessage)
            ->subject('Reset Password')
            ->view('emails.custom-reset-password', ['html' => $html]);
    }
} 
@component('mail::message')
# Reset Password

Hai {{ $user->name }},

Kami menerima permintaan untuk mereset password akun Anda. Silakan klik tombol di bawah ini untuk membuat password baru.

@component('mail::button', ['url' => $url])
Reset Password
@endcomponent

Link reset password ini akan kadaluarsa dalam 60 menit.

Jika Anda tidak meminta reset password, abaikan email ini.

Salam,<br>
{{ config('app.name') }}
@endcomponent 
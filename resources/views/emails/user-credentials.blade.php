@component('mail::message')
# Informasi Akun {{ config('app.name') }}

Hai {{ $user->name }},

Terima kasih telah berbelanja di {{ config('app.name') }}. Berikut adalah informasi akun Anda untuk masuk ke Dashboard Member:

**Email/WhatsApp:** {{ $user->email }} atau {{ $user->whatsapp }}  
**Password:** {{ $password }}

Anda dapat menggunakan kredensial di atas untuk masuk ke akun Anda dengan mengklik tombol di bawah ini:

@component('mail::button', ['url' => $loginUrl])
Masuk ke Akun
@endcomponent

Jika Anda mengalami masalah saat login, silakan hubungi tim dukungan kami.

Salam,<br>
{{ config('app.name') }}
@endcomponent 
<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user cannot login with invalid credentials', function () {
    // Buat user
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    // Coba login dengan password salah
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);
    
    // Pastikan gagal dan ada error
    $response->assertSessionHasErrors(['email']);
    
    // Pastikan user tidak ter-autentikasi
    $this->assertGuest();
});

test('user is locked out after too many login attempts', function () {
    // Buat user
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);
    
    // Coba login dengan password salah sebanyak 5 kali
    foreach (range(0, 5) as $_) {
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
    }
    
    // Coba login dengan password benar, seharusnya sudah terkunci
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    // Pastikan respons menunjukkan terkunci
    $response->assertStatus(429);
});

test('csrf protection prevents form submissions from other domains', function () {
    // Simulasikan request tanpa token CSRF
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    
    // Coba submit form tanpa CSRF token
    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    
    // Seharusnya gagal karena tidak ada CSRF token
    $response->assertStatus(419);
});

test('user with non-admin role cannot access admin routes', function () {
    // Buat user biasa (non-admin)
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Coba akses route admin
    $response = $this->get(route('admin.dashboard'));
    
    // Seharusnya mendapatkan error 403 (forbidden)
    $response->assertForbidden();
}); 
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_media', function (Blueprint $table) {
            $table->id();
            $table->string('platform')->comment('Nama platform media sosial (facebook, instagram, dll)');
            $table->string('username')->comment('Username atau nama akun');
            $table->string('url')->comment('URL lengkap ke akun media sosial');
            $table->string('icon')->nullable()->comment('Nama ikon atau path ke file ikon (opsional)');
            $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
            $table->integer('order')->default(0)->comment('Urutan tampilan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};

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
        Schema::create('admin_whatsapps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama admin atau departemen');
            $table->string('phone_number')->comment('Nomor WhatsApp dalam format internasional');
            $table->text('description')->nullable()->comment('Deskripsi opsional');
            $table->boolean('is_active')->default(true)->comment('Status aktif/nonaktif');
            $table->integer('order')->default(0)->comment('Urutan tampilan');
            $table->timestamp('last_used_at')->nullable()->comment('Waktu terakhir digunakan dalam rotasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_whatsapps');
    }
};

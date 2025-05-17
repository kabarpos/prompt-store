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
        // Hindari membuat tabel jika sudah ada
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('description')->nullable();
                $table->enum('type', ['percentage', 'fixed']);
                $table->decimal('value', 15, 2);
                $table->decimal('min_purchase', 15, 2)->default(0);
                $table->decimal('max_discount', 15, 2)->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('max_uses')->nullable();
                $table->integer('used_count')->default(0);
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu melakukan apa-apa karena migrasi sebelumnya akan menghapus tabel
    }
};

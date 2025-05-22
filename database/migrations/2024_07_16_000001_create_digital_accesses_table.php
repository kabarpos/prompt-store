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
        Schema::create('digital_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('access_code')->unique()->nullable();
            $table->datetime('expires_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('max_downloads')->nullable();
            $table->datetime('last_accessed_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indeks untuk pencarian cepat
            $table->index(['user_id', 'product_id']);
            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_accesses');
    }
}; 
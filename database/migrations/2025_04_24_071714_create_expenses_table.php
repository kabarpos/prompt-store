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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 15, 2); // Jumlah pengeluaran, format decimal untuk menangani mata uang
            $table->date('expense_date');
            $table->foreignId('expense_category_id')->constrained('expense_categories');
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('description')->nullable();
            $table->string('receipt_image')->nullable(); // Path ke bukti pembayaran (gambar)
            $table->enum('payment_method', ['cash', 'transfer', 'credit_card', 'other'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

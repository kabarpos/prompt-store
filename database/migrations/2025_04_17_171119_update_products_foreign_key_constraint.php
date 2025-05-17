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
        Schema::table('products', function (Blueprint $table) {
            // Hapus constraint foreign key yang lama (cascade)
            $table->dropForeign(['category_id']);
            
            // Ubah kolom category_id menjadi nullable
            $table->foreignId('category_id')->nullable()->change();
            
            // Tambahkan kembali foreign key dengan onDelete('set null')
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Hapus constraint yang baru
            $table->dropForeign(['category_id']);
            
            // Kembalikan kolom category_id jadi tidak nullable
            $table->foreignId('category_id')->nullable(false)->change();
            
            // Kembalikan constraint ke onDelete('cascade')
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }
};

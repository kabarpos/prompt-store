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
            // Field kode produk dengan format DLXXX (prefix DL + kombinasi angka dan huruf)
            $table->string('product_code')->nullable()->after('id')->comment('Format: DLXXX');
            
            // Field untuk fitur produk sebagai repeater (stored as JSON)
            $table->json('product_features')->nullable()->after('description')
                  ->comment('Repeater field untuk menampilkan fitur produk');
            
            // Field untuk nilai/keunggulan produk sebagai repeater (stored as JSON)
            $table->json('product_values')->nullable()->after('product_features')
                  ->comment('Repeater field untuk menampilkan nilai/keunggulan produk');
            
            // Field untuk URL demo produk
            $table->string('demo_url')->nullable()->after('custom_url')
                  ->comment('URL untuk tombol demo produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_code',
                'product_features',
                'product_values',
                'demo_url'
            ]);
        });
    }
};

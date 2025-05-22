<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pertama, ambil semua produk dengan hidden_content
        $products = DB::table('products')
            ->whereNotNull('hidden_content')
            ->get(['id', 'hidden_content']);
            
        // Konversi data lama ke format JSON
        foreach ($products as $product) {
            $content = $product->hidden_content;
            // Konversi teks menjadi array dengan satu item
            $jsonContent = json_encode([
                ['content' => $content, 'title' => 'Prompt']
            ]);
            
            DB::table('products')
                ->where('id', $product->id)
                ->update(['hidden_content' => $jsonContent]);
        }
        
        // Ubah tipe kolom menjadi JSON
        Schema::table('products', function (Blueprint $table) {
            $table->json('hidden_content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ambil semua produk dengan hidden_content dalam format JSON
        $products = DB::table('products')
            ->whereNotNull('hidden_content')
            ->get(['id', 'hidden_content']);
            
        // Konversi data JSON kembali ke teks
        foreach ($products as $product) {
            try {
                $jsonContent = json_decode($product->hidden_content, true);
                $content = isset($jsonContent[0]['content']) ? $jsonContent[0]['content'] : '';
                
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['hidden_content' => $content]);
            } catch (\Exception $e) {
                // Jika gagal decode, biarkan apa adanya
            }
        }
        
        // Ubah tipe kolom kembali menjadi longText
        Schema::table('products', function (Blueprint $table) {
            $table->longText('hidden_content')->nullable()->change();
        });
    }
};

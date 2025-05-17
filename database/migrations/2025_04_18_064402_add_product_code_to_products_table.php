<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua produk yang belum memiliki product_code
        $products = DB::table('products')->whereNull('product_code')->get();
        
        $counter = 1;
        foreach ($products as $product) {
            $productCode = 'DL' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            DB::table('products')
                ->where('id', $product->id)
                ->update(['product_code' => $productCode]);
            $counter++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu mengembalikan apa-apa karena kita hanya memperbarui data
    }
};

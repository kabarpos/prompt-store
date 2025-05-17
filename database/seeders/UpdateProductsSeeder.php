<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memperbarui kode produk...');
        
        // Ambil semua produk yang product_code nya NULL
        $products = DB::table('products')->whereNull('product_code')->get();
        
        if ($products->count() === 0) {
            $this->command->info('Semua produk sudah memiliki kode produk.');
            return;
        }
        
        // Generate kode produk untuk setiap produk
        foreach ($products as $product) {
            $productCode = $this->generateUniqueProductCode();
            
            DB::table('products')
                ->where('id', $product->id)
                ->update(['product_code' => $productCode]);
                
            $this->command->info("Produk ID {$product->id} diperbarui dengan kode {$productCode}");
        }
        
        $this->command->info('Selesai memperbarui kode produk.');
    }
    
    /**
     * Generate a unique product code with format DLXXX
     * XXX = random combination of letters and numbers
     */
    private function generateUniqueProductCode()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 3;
        
        do {
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            $productCode = 'DL' . $randomString;
            
            // Cek keunikan kode produk
            $exists = DB::table('products')
                ->where('product_code', $productCode)
                ->exists();
                
        } while ($exists); // Ulangi jika kode sudah digunakan
        
        return $productCode;
    }
}

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
            $table->longText('hidden_content')->nullable()->after('digital_file_path')
                ->comment('Konten tersembunyi yang hanya terlihat setelah pembelian');
            $table->boolean('has_hidden_content')->default(false)->after('is_digital')
                ->comment('Indikator apakah produk memiliki konten tersembunyi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('hidden_content');
            $table->dropColumn('has_hidden_content');
        });
    }
};

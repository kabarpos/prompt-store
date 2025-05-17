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
        Schema::table('coupons', function (Blueprint $table) {
            // Hapus kolom lama jika ada
            if (Schema::hasColumn('coupons', 'name')) {
                $table->dropColumn('name');
            }
            
            if (Schema::hasColumn('coupons', 'minimum_purchase')) {
                $table->renameColumn('minimum_purchase', 'min_purchase');
            }
            
            if (Schema::hasColumn('coupons', 'valid_from')) {
                $table->renameColumn('valid_from', 'starts_at');
            }
            
            if (Schema::hasColumn('coupons', 'valid_to')) {
                $table->renameColumn('valid_to', 'expires_at');
            }
            
            // Tambah kolom baru
            if (!Schema::hasColumn('coupons', 'max_discount')) {
                $table->decimal('max_discount', 15, 2)->nullable()->after('min_purchase');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            // Kembalikan ke struktur awal
            if (Schema::hasColumn('coupons', 'min_purchase')) {
                $table->renameColumn('min_purchase', 'minimum_purchase');
            }
            
            if (Schema::hasColumn('coupons', 'starts_at')) {
                $table->renameColumn('starts_at', 'valid_from');
            }
            
            if (Schema::hasColumn('coupons', 'expires_at')) {
                $table->renameColumn('expires_at', 'valid_to');
            }
            
            if (Schema::hasColumn('coupons', 'max_discount')) {
                $table->dropColumn('max_discount');
            }
            
            if (!Schema::hasColumn('coupons', 'name')) {
                $table->string('name')->after('code');
            }
        });
    }
};

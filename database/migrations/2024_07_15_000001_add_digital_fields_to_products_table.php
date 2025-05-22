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
            $table->boolean('is_digital')->default(false)->after('is_active');
            $table->string('digital_file_path')->nullable()->after('is_digital');
            $table->integer('download_limit')->nullable()->after('digital_file_path');
            $table->integer('access_days')->nullable()->after('download_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_digital', 'digital_file_path', 'download_limit', 'access_days']);
        });
    }
}; 
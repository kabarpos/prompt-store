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
        Schema::table('order_documents', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('is_sent');
            $table->timestamp('read_at')->nullable()->after('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_documents', function (Blueprint $table) {
            $table->dropColumn('is_read');
            $table->dropColumn('read_at');
        });
    }
};

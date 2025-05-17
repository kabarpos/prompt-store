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
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // Drop kolom yang tidak diperlukan
            $table->dropForeign(['template_id']);
            $table->dropColumn(['template_id', 'variables']);
            
            // Ubah tipe kolom status
            $table->string('status')->change();
            
            // Tambah kolom type jika belum ada
            if (!Schema::hasColumn('whatsapp_logs', 'type')) {
                $table->string('type')->default('test')->after('message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus
            $table->foreignId('template_id')->nullable()->constrained('whatsapp_templates')->onDelete('cascade');
            $table->json('variables')->nullable();
            
            // Kembalikan tipe kolom status
            $table->enum('status', ['success', 'failed'])->change();
            
            // Hapus kolom type jika ada
            if (Schema::hasColumn('whatsapp_logs', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}; 
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
        Schema::table('email_settings', function (Blueprint $table) {
            $table->text('reset_password_template')->nullable()->after('verification_template');
            $table->boolean('enable_reset_password_template')->default(true)->after('reset_password_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_settings', function (Blueprint $table) {
            $table->dropColumn('reset_password_template');
            $table->dropColumn('enable_reset_password_template');
        });
    }
}; 
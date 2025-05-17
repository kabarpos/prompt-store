<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Products table indexes
        Schema::table('products', function (Blueprint $table) {
            $table->index('name');
            $table->index('price');
            $table->index('is_active');
            $table->index(['category_id', 'is_active']);
            $table->index('created_at');
        });

        // Orders table indexes
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('total_amount');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });

        // Order items table indexes
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
            $table->index(['order_id', 'product_id']);
        });

        // Services table indexes
        Schema::table('services', function (Blueprint $table) {
            $table->index('title');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        // Products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['price']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['created_at']);
        });

        // Orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['total_amount']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'status']);
        });

        // Order items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['order_id', 'product_id']);
        });

        // Services table
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['title']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
    }
}; 
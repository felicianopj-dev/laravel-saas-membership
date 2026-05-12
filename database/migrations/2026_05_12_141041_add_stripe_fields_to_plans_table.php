<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->text('description')
                ->nullable()
                ->after('slug');
            
            $table->string('currency', 3)
                ->default('usd')
                ->after('price');
            
            $table->string('stripe_product_id')
                ->nullable()
                ->after('billing_interval');
            
            $table->string('stripe_price_id')
                ->nullable()
                ->after('stripe_product_id');
            
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('is_active');
        });
    }
    
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'currency',
                'stripe_product_id',
                'stripe_price_id',
                'sort_order',
            ]);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('barcode')->unique()->nullable();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('commercial_name')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->json('images')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            
            // Prix
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('selling_price_xof', 10, 2)->default(0);
            $table->decimal('selling_price_eur', 10, 2)->default(0);
            $table->decimal('selling_price_usd', 10, 2)->default(0);
            
            // Stock
            $table->integer('stock_quantity')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->string('unit', 10)->default('pcs');
            
            // Taxes
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->boolean('tax_included')->default(true);
            
            // Divers
            $table->boolean('is_active')->default(true);
            $table->boolean('is_trackable')->default(true);
            $table->string('storage_location')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
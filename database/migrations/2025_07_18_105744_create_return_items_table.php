<?php

// database/migrations/2025_07_18_000001_create_return_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnItemsTable extends Migration
{
    public function up()
    {
        // 2025_07_18_000001_create_return_items_table.php
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_return_id')->constrained('returns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('refund_amount', 10, 2);
            $table->timestamps();
        });

        // Ajout de l'index pour le champ product_return_id
        Schema::table('return_items', function (Blueprint $table) {
            $table->index('product_return_id');
        });

    }


    public function down()
    {
        Schema::dropIfExists('return_items');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number')->unique();
            $table->string('customs_reference');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->date('entry_date');
            $table->date('expiry_date');
            $table->decimal('total_value', 12, 2);
            $table->string('currency', 3)->default('XOF');
            $table->enum('status', ['active', 'expired', 'cleared'])->default('active');
            $table->text('description')->nullable();
            $table->json('customs_documents')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lots');
    }
};
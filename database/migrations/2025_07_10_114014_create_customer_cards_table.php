<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->enum('card_type', ['loyalty', 'professional', 'hosted_client', 'electronic_wallet']);
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('company')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->decimal('wallet_balance', 10, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('flight_info')->nullable(); // Info depuis carte d'embarquement
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_cards');
    }
};
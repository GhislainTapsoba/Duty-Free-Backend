<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'wallet', 'multiple']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('XOF');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->decimal('amount_in_base_currency', 10, 2);
            
            // Informations spécifiques au mode de paiement
            $table->string('card_type')->nullable(); // Visa, MasterCard, etc.
            $table->string('card_last_four')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('authorization_code')->nullable();
            $table->string('mobile_money_provider')->nullable();
            $table->string('mobile_money_number')->nullable();
            
            // Informations TPE
            $table->string('terminal_id')->nullable();
            $table->string('merchant_id')->nullable();
            
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamp('payment_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('external_data', function (Blueprint $table) {
            $table->id();
            $table->string('data_type'); // 'monthly_passengers', 'exchange_rates', etc.
            $table->date('period_date');
            $table->json('data');
            $table->string('source')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['data_type', 'period_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('external_data');
    }
};
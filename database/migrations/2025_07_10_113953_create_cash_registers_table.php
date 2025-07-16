<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('location');
            $table->string('ip_address')->nullable();
            $table->json('printer_config')->nullable();
            $table->json('scanner_config')->nullable();
            $table->json('tpe_config')->nullable();
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();

            // Colonnes supplémentaires pour gestion des ouvertures/fermetures
            $table->boolean('is_open')->default(false);
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            // Foreign keys si nécessaire (optionnel selon ta logique)
            $table->foreign('opened_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_registers');
    }
};

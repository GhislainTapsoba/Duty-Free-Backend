<?php

// database/migrations/2025_07_18_000000_create_returns_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTable extends Migration
{
    public function up()
    {
        // 2025_07_18_000000_create_returns_table.php
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('returned_by_user_id')->nullable();
            $table->foreign('returned_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('status');
            $table->decimal('total_refund_amount', 10, 2);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
        // Ajout de l'index pour le champ returned_by_user_id
        Schema::table('returns', function (Blueprint $table) {
            $table->index('returned_by_user_id');
        });



    }

    public function down()
    {
        Schema::dropIfExists('returns');
    }
}

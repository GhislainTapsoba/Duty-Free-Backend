<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('roles', function ($table) {
        $table->string('description')->nullable()->after('name');
    });

        Schema::table('permissions', function ($table) {
            $table->string('description')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('roles', function ($table) {
            $table->dropColumn('description');
        });

        Schema::table('permissions', function ($table) {
            $table->dropColumn('description');
        });
    }

};

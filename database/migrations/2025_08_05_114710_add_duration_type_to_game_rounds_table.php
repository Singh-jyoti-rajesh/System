<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_rounds', function (Blueprint $table) {
            $table->string('duration_type')->default('1min'); // values: '30sec', '1min', '3min', '5min'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_rounds', function (Blueprint $table) {
            //
        });
    }
};

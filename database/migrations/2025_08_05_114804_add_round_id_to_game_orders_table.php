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
        Schema::table('game_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable()->after('result');
        });
    }
    public function down()
    {
        Schema::table('game_orders', function (Blueprint $table) {
            $table->dropColumn('round_id');
        });
    }
};

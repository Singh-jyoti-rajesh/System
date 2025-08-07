<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameRoundsTable extends Migration
{
    public function up()
    {
        Schema::create('game_rounds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('counter_id');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable(); // Fix: make it nullable
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_rounds');
    }
}

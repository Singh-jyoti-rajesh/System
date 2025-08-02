<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('leader_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User who applied
            $table->unsignedBigInteger('sent_to'); // Admin ID
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sent_to')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('leader_requests');
    }
}

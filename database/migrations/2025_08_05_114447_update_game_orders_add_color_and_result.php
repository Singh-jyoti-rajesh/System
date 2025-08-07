<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Add new ENUM value 'color' to game_type column
        DB::statement("ALTER TABLE game_orders MODIFY COLUMN game_type ENUM('big', 'small', 'color') NOT NULL");

        // Add 'color' column if not exists
        if (!Schema::hasColumn('game_orders', 'color')) {
            Schema::table('game_orders', function (Blueprint $table) {
                $table->string('color')->nullable()->after('game_type'); // red, green, blue
            });
        }

        // Add 'result' column if not exists
        if (!Schema::hasColumn('game_orders', 'result')) {
            Schema::table('game_orders', function (Blueprint $table) {
                $table->enum('result', ['pending', 'win', 'lose'])->default('pending')->after('total_amount');
            });
        }
    }

    public function down(): void
    {
        // Revert game_type ENUM (if needed)
        DB::statement("ALTER TABLE game_orders MODIFY COLUMN game_type ENUM('big', 'small') NOT NULL");

        Schema::table('game_orders', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->dropColumn('result');
        });
    }
};

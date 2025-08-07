<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    protected $fillable = ['counter_id', 'start_time', 'end_time', 'duration_type'];


    public $timestamps = true;

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];
}

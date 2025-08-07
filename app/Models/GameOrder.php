<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'game_type',
        'color',
        'number',         // ✅ Add this line
        'balance',
        'quantity',
        'multiplier',
        'total_amount',
        'result',
        'round_id'
    ];



    // App\Models\GameOrder.php

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function round()
    {
        return $this->belongsTo(GameRound::class, 'round_id');
    }
}

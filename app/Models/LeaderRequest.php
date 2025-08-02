<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'sent_to', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'sent_to');
    }
}

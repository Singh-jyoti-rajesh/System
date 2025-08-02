<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'invitation_code',
        'invited_by',
        'status', // 0 for default , 1 for leader apply  2. for reject leader apply  3. for accept leader apply
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // User who invited this user
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Alias method (optional, same as invitedBy)
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Direct users invited by this user
    public function directSubordinates()
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    // Recursive team subordinates (all levels)
    public function getRecursiveTeamSubordinates()
    {
        $team = collect();

        foreach ($this->directSubordinates as $subordinate) {
            $team->push($subordinate);
            $team = $team->merge($subordinate->getRecursiveTeamSubordinates());
        }

        return $team;
    }
    public function leaderRequest()
    {
        return $this->hasOne(LeaderRequest::class);
    }


    // Optional: HasManyThrough — Not recursive, for deeper access if needed
    public function teamSubordinates()
    {
        return $this->hasManyThrough(
            User::class,
            User::class,
            'invited_by',
            'invited_by',
            'id',
            'id'
        );
    }
}

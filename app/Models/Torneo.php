<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Torneo extends Model
{
    use HasFactory;

    protected $table = 'torneos';

    protected $fillable = ['nombre', 'fecha'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'torneos_users', 'torneo_id', 'discord_id');
    }
}
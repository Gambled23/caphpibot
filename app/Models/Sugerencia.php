<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sugerencia extends Model
{
    use HasFactory;

    protected $table = 'sugerencias';

    protected $fillable = [
        'campeon',
        'build',
        'rol',
        'discord_id',
        'votos',
        'jugado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'discord_id', 'discord_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameQuestion extends Model
{
    protected $fillable = ['title', 'session', 'options', 'image'];

    protected $cast = [
        "options" => 'array',
    ];

    public function gameAnswers() 
    {
        return $this->hasMany(GameAnswer::class);
    }
}

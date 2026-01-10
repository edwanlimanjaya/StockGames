<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAnswer extends Model
{
    protected $fillable = ['game_question_id', 'user_id', 'choices', 'reasons'];

    public function gameQuestions()
    {
        return $this->belongsTo(GameQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);    
    }
}

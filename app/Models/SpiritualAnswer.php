<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiritualAnswer extends Model
{
    protected $fillable = ['spiritual_question_id', 'user_id', 'score'];

    public function spiritualQuestions()
    {
        return $this->belongsTo(SpiritualQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);    
    }
}

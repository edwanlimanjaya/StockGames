<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiteracyAnswer extends Model
{

    protected $fillable = ['literacy_question_id', 'user_id', 'content'];

    public function literacyQuestions()
    {
        return $this->belongsTo(LiteracyQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);    
    }
}

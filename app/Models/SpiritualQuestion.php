<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpiritualQuestion extends Model
{
    protected $fillable = ['title', 'direction'];

    public function spiritualAnswers() 
    {
        return $this->hasMany(SpiritualAnswer::class);
    }
}

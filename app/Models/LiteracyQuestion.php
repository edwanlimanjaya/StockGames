<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiteracyQuestion extends Model
{
    protected $fillable = ['title', 'type', 'options'];

    protected $cast = [
        "options" => 'array',
    ];

    public function literacyAnswers() 
    {
        return $this->hasMany(LiteracyAnswer::class);
    }
}

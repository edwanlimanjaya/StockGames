<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['type', 'name', 'description', 'code', 'return', 'risk'];
}

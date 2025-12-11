<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabalhador extends Model
{
    protected $fillable = ['nome', 'especialidade', 'valorHora', 'status', 'qualidade', 'deleted'];

    // protected $casts = ['valorHora' => 'float', 'qualidade' => 'float'];
}
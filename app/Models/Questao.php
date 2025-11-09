<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questao extends Model
{
    protected $fillable = ['texto_questao', 'area_concurso', 'diciplina', 'nivel_dificuldade', 'gabarito_correto', 'deleted'];

    // protected $casts = [];
}
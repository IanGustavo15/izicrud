<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metrica extends Model
{
    protected $fillable = ['id_simulado', 'media_geral_pontuacao', 'base_vagas', 'deleted'];

    // protected $casts = ['media_geral_pontuacao' => 'float'];
}
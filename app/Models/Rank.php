<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $fillable = ['id_simulado', 'id_users', 'pontuacao_final', 'posicao_rank', 'classificacao', 'deleted'];

    // protected $casts = [];
}
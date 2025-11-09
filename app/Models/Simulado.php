<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulado extends Model
{
    protected $fillable = ['titulo', 'descricao', 'data_inicio', 'data_fim', 'duracao_minutos', 'numero_vagas', 'deleted'];

    // protected $casts = ['data_inicio' => 'date', 'data_fim' => 'date'];
}
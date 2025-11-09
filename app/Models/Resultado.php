<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $fillable = ['id_inscricao', 'pontuacao_total', 'acertos', 'erros', 'tempo_total_minutos', 'percentual_acerto', 'deleted'];

    // protected $casts = ['percentual_acerto' => 'float'];
}
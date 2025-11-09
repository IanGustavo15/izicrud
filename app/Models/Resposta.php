<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    protected $fillable = ['id_inscricao', 'id_questao', 'resposta_selecionada', 'tempo_resposta_segundos', 'correta', 'deleted'];

    // protected $casts = [];
}
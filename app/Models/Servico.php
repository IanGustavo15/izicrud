<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $fillable = ['nome', 'descricao', 'preco_mao_de_obra', 'tempo_estimado', 'deleted'];

    // protected $casts = ['preco_mao_de_obra' => 'float'];
}
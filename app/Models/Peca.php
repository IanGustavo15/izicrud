<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peca extends Model
{
    protected $fillable = ['descricao', 'codigo_unico', 'preco_de_custo', 'preco_de_venda', 'quantidade', 'estoque', 'deleted'];

    // protected $casts = ['preco_de_custo' => 'float', 'preco_de_venda' => 'float'];
}
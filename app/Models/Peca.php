<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peca extends Model
{
    protected $fillable = ['descricao', 'codigo_unico', 'preco_de_custo', 'preco_de_venda', 'quantidade', 'estoque', 'deleted'];

    public function servicosPadrao()
    {
        return $this->belongsToMany(Servico::class, 'pecaservicos', 'peca_id', 'servico_id')->withPivot('quantidade_peca', 'deleted')->withTimestamps()->using(PecaServico::class);
    }


    // protected $casts = ['preco_de_custo' => 'float', 'preco_de_venda' => 'float'];
}

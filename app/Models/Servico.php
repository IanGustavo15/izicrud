<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $fillable = ['nome', 'descricao', 'preco_mao_de_obra', 'tempo_estimado', 'quantidade_peca', 'deleted'];

    public function pecasPadrao()
    {
        return $this->belongsToMany(Peca::class, 'pecaservicos', 'id_servico', 'id_peca')->withPivot('quantidade_peca', 'deleted')->withTimestamps()->using(PecaServico::class);
    }

    // protected $casts = ['preco_mao_de_obra' => 'float'];
}

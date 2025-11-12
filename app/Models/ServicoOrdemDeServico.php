<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoOrdemDeServico extends Model
{
    protected $fillable = ['id_ordemdeservico', 'id_servico', 'quantidade', 'preco_unitario', 'deleted'];

    protected $table = 'servicoordemdeservicos';


    public function servico()
    {
        return $this->belongsTo(Servico::class, 'id_servico', 'id');
    }

    public function ordemDeServico()
    {
        return $this->belongsTo(OrdemDeServico::class, 'id_ordemdeservico', 'id');
    }
    // protected $casts = ['preco_unitario' => 'float'];
}

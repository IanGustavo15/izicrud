<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemDeServico extends Model
{
    protected $fillable = ['id_cliente', 'id_veiculo', 'data_de_entrada', 'data_de_saida', 'status', 'valor_total', 'observacao', 'deleted'];

    protected $table = 'ordemdeservicos';

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'id_veiculo', 'id');
    }
    // protected $casts = ['data_de_entrada' => 'date', 'data_de_saida' => 'date', 'valor_total' => 'float'];
}

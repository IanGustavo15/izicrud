<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $fillable = ['id_cliente', 'placa', 'modelo', 'ano', 'tipo', 'deleted'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }
    // protected $casts = [];
}

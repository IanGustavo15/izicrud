<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;

class Moto extends Model
{
    protected $fillable = ['modelo', 'placa', 'id_cliente', 'deleted'];

    // protected $casts = [];

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

}

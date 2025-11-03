<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Moto;

class Cliente extends Model
{
    protected $fillable = ['nome', 'cpf', 'contato', 'deleted'];

    // protected $casts = [];
    public function moto(){
        return $this->hasMany(Moto::class, 'id_cliente', 'id');
    }
}

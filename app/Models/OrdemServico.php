<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServico extends Model
{
    protected $table = "ordemservicos";
    protected $fillable = ['id_servico', 'id_moto', 'data_servico', 'realizado', 'deleted'];

    // protected $casts = ['data_servico' => 'date'];

    public function moto(){
        return $this->hasOne(Moto::class,'id','id_moto');
    }

    public function servico(){
        return $this->hasOne(Servico::class,'id','id_servico');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimuladoQuestao extends Model
{
    protected $table = "simuladoquestaos";
    protected $fillable = ['id_simulado', 'id_questao', 'deleted'];

    // protected $casts = [];
}

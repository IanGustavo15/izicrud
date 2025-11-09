<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCorte extends Model
{
    protected $table = "notacortes";
    protected $fillable = ['id_simulado', 'valor_corte', 'deleted'];

    // protected $casts = [];
}

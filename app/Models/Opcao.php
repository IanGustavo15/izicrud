<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opcao extends Model
{
    protected $fillable = ['id_questao', 'letra', 'texto_opcao', 'deleted'];

    // protected $casts = [];
}
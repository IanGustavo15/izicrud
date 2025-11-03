<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $fillable = ['descricao', 'valor', 'deleted'];

    // protected $casts = ['valor' => 'float'];
}
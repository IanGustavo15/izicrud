<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    protected $fillable = ['id_users', 'data_inscricao', 'status', 'deleted'];

    // protected $casts = ['data_inscricao' => 'date'];
}
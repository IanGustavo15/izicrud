<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = ['nome', 'email', 'cpf', 'telefone', 'foto', 'deleted'];
    protected $displayLabels = ['Nome', 'E-mail', 'CPF', 'Telefone', 'Foto'];

    protected $casts = [];


}

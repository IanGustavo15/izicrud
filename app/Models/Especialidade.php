<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidade extends Model
{
    protected $fillable = ['especialidade', 'deleted'];

    public function trabalhador()
    {
        return $this->belongsTo(Trabalhador::class, 'id_especialidade', 'especialidade');
    }

    // protected $casts = [];
}

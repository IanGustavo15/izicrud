<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PecaServico extends Model
{
    protected $fillable = ['id_servico', 'id_peca', 'quantidade_peca', 'deleted'];

    protected $table = 'pecaservicos';

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'id_servico', 'id');
    }

    public function peca()
    {
        return $this->belongsTo(Peca::class, 'id_peca', 'id');
    }

    // protected $casts = [];
}

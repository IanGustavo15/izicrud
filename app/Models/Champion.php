<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Champion extends Model
{
    protected $fillable = [
        'key',
        'api_id',
        'name',
        'title',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array'
    ];
}

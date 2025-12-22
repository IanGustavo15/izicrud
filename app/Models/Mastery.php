<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mastery extends Model
{
    protected $fillable = ['player', 'points', 'champion'];

    protected $table = 'masteries';
}

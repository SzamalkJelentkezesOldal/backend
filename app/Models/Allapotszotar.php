<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allapotszotar extends Model
{
    /** @use HasFactory<\Database\Factories\AllapotszotarFactory> */
    use HasFactory;
    protected $fillable = ['elnevezes'];

}

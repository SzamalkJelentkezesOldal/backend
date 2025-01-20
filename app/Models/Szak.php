<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Szak extends Model
{
    /** @use HasFactory<\Database\Factories\SzakFactory> */
    use HasFactory;
    protected $fillable = [
        'elnevezes',
        'portfolio',
        'nappali'
    ];
}

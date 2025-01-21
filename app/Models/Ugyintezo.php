<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ugyintezo extends Model
{
    /** @use HasFactory<\Database\Factories\UgyintezoFactory> */
    use HasFactory;
    protected $fillable = [
        'nev',
        'email',
        'jelszo',
        'jelszoMegerosites',
        'master',
    ];
}

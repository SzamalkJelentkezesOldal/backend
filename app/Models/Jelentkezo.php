<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jelentkezo extends Model
{
    /** @use HasFactory<\Database\Factories\JelentkezoFactory> */
    use HasFactory;

    protected $fillable = [
        'nev',
        'email',
        'tel',
        'token'
    ];
}

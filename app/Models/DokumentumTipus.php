<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumentumTipus extends Model
{
    /** @use HasFactory<\Database\Factories\DokumentumTipusFactory> */
    use HasFactory;

    protected $fillable = [
        'elnevezes'
    ];
}

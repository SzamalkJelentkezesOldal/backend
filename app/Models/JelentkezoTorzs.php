<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JelentkezoTorzs extends Model
{
    /** @use HasFactory<\Database\Factories\JelentkezoTorzsFactory> */
    use HasFactory;
    protected $fillable = [
        'jelentkezo_:id',
        'vezeteknev',
        'keresztnev',
        'adoszam',
        'szemelyi_szam',
        'lakcim',
        'taj_szam',
        'nem',
        'anyja_neve',
        'szuletesi_hely',
        'szuletesi_nev',
        'szuletesi_datum',
        'allampolgarsag',
        'elozo_iskola_nev',
        'elozo_iskola_hely'
    ];
}

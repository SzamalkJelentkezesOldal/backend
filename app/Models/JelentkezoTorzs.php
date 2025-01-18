<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JelentkezoTorzs extends Model
{
    protected $primaryKey = 'jelentkezo_id';
    public $incrementing = false;
    /** @use HasFactory<\Database\Factories\JelentkezoTorzsFactory> */
    use HasFactory;
    protected $fillable = [
        'jelentkezo_id',
        'vezeteknev',
        'keresztnev',
        'adoazonosito',
        'lakcim',
        'taj_szam',
        'anyja_neve',
        'szuletesi_hely',
        'szuletesi_nev',
        'szuletesi_datum',
        'allampolgarsag',
        'szulo_elerhetoseg'
    ];
}

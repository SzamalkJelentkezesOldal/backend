<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumentumok extends Model
{
    /** @use HasFactory<\Database\Factories\DokumentumokFactory> */
    use HasFactory;
    protected $fillable = [
        'jelentkezo_id',
        'adoszam_foto',
        'taj_szam_foto',
        'szemelyi_foto_elol',
        'szemelyi_foto_hatul',
        'lakcim_foto_elol',
        'lakcim_foto_hatul',
        'erettsegi_biz',
        'tanulmanyi_fotok'
    ];
}

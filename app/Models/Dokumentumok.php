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
        'dokumentum_tipus_id',
        'fajlok'
    ];
    
    protected $casts = [
        'fajlok' => 'array',
    ];

    public function jelentkezo()
    {
        return $this->belongsTo(Jelentkezo::class, 'jelentkezo_id');
    }

    
    public function tipus()
    {
        return $this->belongsTo(DokumentumTipus::class, 'dokumentum_tipus_id');
    }
}

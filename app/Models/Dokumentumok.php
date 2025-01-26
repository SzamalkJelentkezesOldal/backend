<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumentumok extends Model
{
    protected $primaryKey = 'jelentkezo_id';
    /** @use HasFactory<\Database\Factories\DokumentumokFactory> */
    use HasFactory;
    protected $fillable = [
        'jelentkezo_id',
        'dokumentum_id',
        'dokumentum_url'
    ];

    public function jelentkezo()
    {
        return $this->belongsTo(Jelentkezo::class, 'jelentkezo_id');
    }

    
    public function tipus()
    {
        return $this->belongsTo(DokumentumTipus::class, 'dokumentum_id');
    }
}

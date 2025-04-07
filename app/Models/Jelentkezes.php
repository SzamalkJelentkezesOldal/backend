<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jelentkezes extends Model
{

    /** @use HasFactory<\Database\Factories\JelentkezesFactory> */
    use HasFactory;

    protected $fillable = [
        'szak_id',
        'jelentkezo_id',
        'allapot',
        'sorrend',
    ];

    public function jelentkezo(){
        return $this->belongsTo(Jelentkezo::class,'jelentkezo_id','id');
    }

    public function allapotszotar()
    {
        return $this->belongsTo(Allapotszotar::class, 'allapot');
    }

    public function szak()
    {
        return $this->belongsTo(Szak::class, 'szak_id');
    }

    public function statuszvaltozasok()
    {
        return $this->hasMany(Statuszvaltozas::class, 'jelentkezo_id', 'jelentkezo_id');
    }
}

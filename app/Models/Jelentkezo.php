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

    // 3. Jelentkezőhöz tartozó dokumentumok (1->N)
    public function dokumentumok()
    {
        return $this->hasMany(Dokumentumok::class, 'jelentkezo_id');
    }

    // jelentkezo modelba 
    public function jelentkezesek()
    {
        return $this->hasMany(Jelentkezes::class, 'jelentkezo_id', 'id');
    }

    // jelentkezo modelba
    public function torzsadatok()
    {
        return $this->hasOne(JelentkezoTorzs::class, 'jelentkezo_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}

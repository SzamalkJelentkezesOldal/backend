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

    public function jelentkezesek()
    {
        return $this->hasMany(Jelentkezes::class, 'jelentkezo_id', 'id');
    }
    public function torzsadatok()
    {
        return $this->hasOne(JelentkezoTorzs::class, 'jelentkezo_id', 'id');
    }
}

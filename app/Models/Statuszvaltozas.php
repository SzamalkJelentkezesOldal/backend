<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statuszvaltozas extends Model
{

    protected $fillable = [
        'jelentkezo_id',
        'szak_id',
        'allapot',
        'modositas_ideje',
        'user_id'
    ];


    public function jelentkezo()
    {
        return $this->belongsTo(Jelentkezo::class, 'jelentkezo_id');
    }

    
    public function szak()
    {
        return $this->belongsTo(Szak::class, 'szak_id');
    }

    
    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

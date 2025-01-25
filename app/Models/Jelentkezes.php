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
        'sorrend'
    ];


    // jelentkezes modelba
    public function jelentkezo(){
        return $this->belongsTo(Jelentkezo::class,'jelentkezo_id','id');
    }
}

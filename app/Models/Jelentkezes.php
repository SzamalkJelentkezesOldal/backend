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

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('szak_id', '=', $this->getAttribute('szak_id'))
            ->where('jelentkezo_id', '=', $this->getAttribute('jelentkezo_id'));

        return $query;
    }

    // jelentkezes modelba
    public function jelentkezo(){
        return $this->belongsTo(Jelentkezo::class,'jelentkezo_id','id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{

    // protected $primaryKey = ['jelentkezo_id', 'portfolio_url', 'szak_id'];
    /** @use HasFactory<\Database\Factories\PortfolioFactory> */
    use HasFactory;
    protected $fillable = [
        'jelentkezo_id',
        'portfolio_url',
        'szak_id'
    ];
}

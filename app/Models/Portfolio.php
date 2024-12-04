<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    /** @use HasFactory<\Database\Factories\PortfolioFactory> */
    use HasFactory;
    protected $fillable = [
        'jelentkezes_id',
        'portfolio_url',
    ];

    protected function setKeysForSaveQuery($query)
    {
        $query
            ->where('jelentkezes_id', '=', $this->getAttribute('jelentkezes_id'));

        return $query;
    }
}

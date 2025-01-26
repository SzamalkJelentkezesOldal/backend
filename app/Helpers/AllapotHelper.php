<?php

namespace App\Helpers;

use App\Enums\Allapot;
use App\Models\Allapotszotar;

class AllapotHelper
{
    public static function getId(Allapot $allapot): int
    {
        // Cache-eljük az állapotokat, hogy ne kelljen minden alkalommal lekérdezni az adatbázist
        $allapotok = cache()->rememberForever('allapotszotar', function () {
            return Allapotszotar::pluck('id', 'elnevezes')->toArray();
        });

        return $allapotok[$allapot->value];
    }
}
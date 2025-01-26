<?php

namespace App\Helpers;

use App\Enums\Allapot;
use App\Models\Allapotszotar;

class AllapotHelper
{
    public static function getId(string $allapotNev): int
    {
        return Allapotszotar::where('elnevezes', $allapotNev)->first()->id;
    }
}
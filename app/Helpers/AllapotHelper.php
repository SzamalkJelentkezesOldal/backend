<?php

namespace App\Helpers;

use App\Enums\Allapot;
use App\Models\Allapotszotar;
use App\Models\Statuszvaltozas;

class AllapotHelper
{
    public static function getId(string $allapotNev): int
    {
        return Allapotszotar::where('elnevezes', $allapotNev)->first()->id;
    }

    public static function hasStatusEvent($jelentkezoId, string $statusNev): bool
    {
        // Feltételezve, hogy a Statuszvaltozas modellnek van egy allapotszotar() kapcsolata,
        // ami az allapotszotars táblára hivatkozik.
        return Statuszvaltozas::where('jelentkezo_id', $jelentkezoId)
            ->whereHas('ujAllapot', function ($query) use ($statusNev) {
                $query->where('elnevezes', $statusNev);
            })->exists();
    }
}
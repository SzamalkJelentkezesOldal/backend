<?php

namespace App\Http\Controllers;

use App\Models\Jelentkezo;
use Illuminate\Support\Facades\DB;

class JelentkezesController extends Controller
{
    public function countJelentkezesSzama(String $id)
    {
        $jelentkezo = Jelentkezo::with('jelentkezesek');

        // Jelentkezések számának meghatározása
        $szakokSzama = $jelentkezo->jelentkezesek->count();

        return response()->json([
            'jelentkezo_id' => $id,
            'jelentkezesek_szama' => $szakokSzama
        ]);
    }

    public function elfogadottakSzakonkent()
    {
        $szakonkentiElfogadottak = DB::table('jelentkezes')
            ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
            ->select('szaks.elnevezes as szak_nev','szaks.id' , DB::raw('COUNT(jelentkezes.jelentkezo_id) as elfogadottak_szama'))
            ->where('jelentkezes.allapot', '=', 'Elfogadva') // Csak az "Elfogadva" állapotú jelentkezések
            ->groupBy('szak_nev', 'szaks.id') // Csoportosítás szakonként
            ->get();
        return $szakonkentiElfogadottak;
    }
}

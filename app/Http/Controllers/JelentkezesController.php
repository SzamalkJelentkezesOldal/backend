<?php

namespace App\Http\Controllers;

use App\Models\Jelentkezo;

class JelentkezesController extends Controller
{
    public function countJelentkezesSzama(String $id)
    {
        $jelentkezo = Jelentkezo::with('jelentkezesek')->findOrFail($id);

        // Jelentkezések számának meghatározása
        $szakokSzama = $jelentkezo->jelentkezesek->count();

        return response()->json([
            'jelentkezo_id' => $id,
            'jelentkezesek_szama' => $szakokSzama
        ]);
    }
}

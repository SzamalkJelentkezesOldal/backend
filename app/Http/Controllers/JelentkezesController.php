<?php

namespace App\Http\Controllers;

use App\Models\Jelentkezo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function allapotValtozas(Request $request)
    {
        $rules = [
            'jelentkezo_id' => 'required|exists:jelentkezos,id', 
            'szak_id' => 'required|exists:szaks,id', 
            'allapot' => 'required|string|max:50', 
        ];
    
        // Validáció
        $validator = Validator::make($request->all(), $rules);
    
        // Ha a validáció sikertelen, hibaválaszt adunk vissza
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Hibás adatok.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        // Validált adatok lekérése
        $validated = $validator->validated();
    
        // A jelentkezés állapotának frissítése az adott jelentkező és szak párosítás alapján
        DB::table('jelentkezes')
            ->where('jelentkezo_id', $validated['jelentkezo_id']) 
            ->where('szak_id', $validated['szak_id']) 
            ->update([
                'allapot' => $validated['allapot'],
            ]);
    
        return response()->json([
            'message' => 'Jelentkezés állapota frissítve.',
        ]);
    }
}

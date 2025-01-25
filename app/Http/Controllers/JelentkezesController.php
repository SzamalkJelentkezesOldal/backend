<?php

namespace App\Http\Controllers;

use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function getJelentkezesek(String $email)
    {
        $jelentkezoId = DB::table('jelentkezos')->where('email', $email)->value('id');

        $jelentkezesek = Jelentkezes::where('jelentkezo_id', $jelentkezoId)
        ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
        ->select('jelentkezes.sorrend', 'szaks.elnevezes', 'jelentkezes.jelentkezo_id', 'jelentkezes.szak_id')
        ->get();

        return response()->json($jelentkezesek);
    }

    public function updateSorrend(Request $request, $jelentkezo_id)
    {
        $jelentkezo = DB::table('jelentkezos')->where('id', $jelentkezo_id)->first();

        $validatedData = $request->validate([
            'jelentkezesek' => 'required|array',
            'jelentkezesek.*.szak_id' => 'required|integer|exists:szaks,id',
            'jelentkezesek.*.sorrend' => 'required|integer',
        ]);

        try {
            foreach ($validatedData['jelentkezesek'] as $jelentkezes) {
                DB::table('jelentkezes')
                    ->where('szak_id', $jelentkezes['szak_id'])
                    ->where('jelentkezo_id', $jelentkezo->id)
                    ->update([
                        // 'allapot' => 2,
                        'sorrend' => $jelentkezes['sorrend'],
                        // 'created_at' => $jelentkezo->created_at,
                        // 'updated_at' => now(),
                    ]);
            }
            
            Log::info(DB::getQueryLog());

            return response()->json(['message' => 'Sorrend sikeresen frissítve.']);
        } catch (\Exception $e) {
            Log::error('Hiba történt: ' . $e->getMessage());
            return response()->json([
                'error' => 'Belső hiba történt: ' . $e->getMessage(),
            ], 500);
        }
    }

}

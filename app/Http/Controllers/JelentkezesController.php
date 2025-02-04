<?php

namespace App\Http\Controllers;

use App\Helpers\AllapotHelper;
use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use App\Models\Statuszvaltozas;
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
        ->select('jelentkezes.sorrend', 'szaks.portfolio', 'szaks.elnevezes', 'jelentkezes.jelentkezo_id', 'jelentkezes.szak_id')
        ->get();

        return response()->json($jelentkezesek);
    }

    public function updateSorrend(Request $request, $jelentkezo, $beiratkozik)
    {
        try {
            Log::info('Params:', ['jelentkezo' => $jelentkezo, 'beiratkozik' => $beiratkozik]);
            
            $jelentkezoRecord = DB::table('jelentkezos')->find($jelentkezo);
            
            if (!$jelentkezoRecord) {
                Log::error("Nem létező jelentkező: $jelentkezo");
                return response()->json(['error' => 'Nem létező jelentkező'], 404);
            }

            $validatedData = $request->validate([
                'jelentkezesek' => 'required|array',
                'jelentkezesek.*.szak_id' => 'required|integer|exists:szaks,id',
                'jelentkezesek.*.sorrend' => 'required|integer',
            ]);

            Log::info('Validált adatok:', $validatedData);

            foreach ($validatedData['jelentkezesek'] as $jelentkezes) {
                // 1. Ellenőrizzük a jelentkezési rekord létezését
                $existing = DB::table('jelentkezes')
                    ->where('szak_id', $jelentkezes['szak_id'])
                    ->where('jelentkezo_id', $jelentkezo)
                    ->first();

                if (!$existing) {
                    Log::error("Nem létező jelentkezési rekord: ", $jelentkezes);
                    continue;
                }

                // 2. Frissítés
                $updateData = ['sorrend' => $jelentkezes['sorrend']];
                
                if ($beiratkozik) {
                    $ujAllapot = AllapotHelper::getId("Eldöntésre vár");
                    
                    // 3. Ellenőrizzük az állapotot
                    if (!$ujAllapot) {
                        Log::error("Ismeretlen állapot: Eldöntésre vár");
                        throw new \Exception("Hibás állapot azonosító");
                    }
                    
                    $updateData['allapot'] = $ujAllapot;

                    // 4. Státuszváltozás rögzítése
                    Statuszvaltozas::create([
                        'jelentkezo_id' => $jelentkezo,
                        'szak_id' => $jelentkezes['szak_id'],
                        'regi_allapot' => $existing->allapot,
                        'uj_allapot' => $ujAllapot,
                        'user_id' => null,
                    ]);
                }

                DB::table('jelentkezes')
                    ->where('id', $existing->id)
                    ->update($updateData);
            }

            return response()->json(['message' => 'Sorrend sikeresen frissítve']);
            
        } catch (\Exception $e) {
            Log::error('HIBA: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'error' => 'Váratlan hiba: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getJelentkezesAllapot($email) {
        $jelentkezo = DB::table('jelentkezos')->where('email', $email)->first();

        $allapot = Jelentkezes::where('jelentkezo_id', $jelentkezo->id)
                    ->join('allapotszotars', 'jelentkezes.allapot', '=', 'allapotszotars.id')
                    ->select('allapotszotars.elnevezes', 'jelentkezes.jelentkezo_id')->first();

        return response()->json($allapot);
    }

}

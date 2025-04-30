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
    public function countJelentkezesSzama($jelentkezo_id)
    {
        $jelentkezo = Jelentkezo::findOrFail($jelentkezo_id);
        $count = $jelentkezo->jelentkezesek()->count(); 
        return response()->json($count, 200); 
    }

    public function elfogadottakSzakonkent()
    {
        $szakonkentiElfogadottak = DB::table('jelentkezes')
            ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
            ->select('szaks.elnevezes as szak_nev', 'szaks.id', DB::raw('COUNT(jelentkezes.jelentkezo_id) as elfogadottak_szama'))
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

    public function getJelentkezesek($email)
    {
        $jelentkezoId = DB::table('jelentkezos')->where('email', $email)->value('id');

        $jelentkezesek = Jelentkezes::where('jelentkezo_id', $jelentkezoId)
            ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
            ->select('jelentkezes.sorrend', 'szaks.portfolio', 'szaks.elnevezes', 'jelentkezes.jelentkezo_id', 'jelentkezes.szak_id')
            ->get();

        return response()->json($jelentkezesek);
    }

    public function modositasVegrehajtas($email) {
        try {
            // Find jelentkezo_id by email
            $jelentkezo = DB::table('jelentkezos')->where('email', $email)->first();

            if (!$jelentkezo) {
                return response()->json(['error' => 'Nem található jelentkező ezzel az email címmel'], 404);
            }

            $modositasraVarId = AllapotHelper::getId("Módosításra vár");
            $eldontesreVarId = AllapotHelper::getId("Eldöntésre vár");

            $jelentkezesek = Jelentkezes::where('jelentkezo_id', $jelentkezo->id)
                ->where('allapot', $modositasraVarId)
                ->get();

            if ($jelentkezesek->isEmpty()) {
                return response()->json(['message' => 'Nincsenek módosításra váró jelentkezések'], 200);
            }

            foreach ($jelentkezesek as $jelentkezes) {
                $regiAllapot = $jelentkezes->allapot;
                
                $jelentkezes->allapot = $eldontesreVarId;
                $jelentkezes->save();

                Statuszvaltozas::create([
                    'jelentkezo_id' => $jelentkezo->id,
                    'szak_id' => $jelentkezes->szak_id,
                    'regi_allapot' => $regiAllapot,
                    'uj_allapot' => $eldontesreVarId,
                    'user_id' => null,
                ]);
            }

            return response()->json([
                'message' => 'Jelentkezések állapota sikeresen frissítve',
                'jelentkezesek_szama' => $jelentkezesek->count()
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('HIBA: ' . $e->getMessage());
            return response()->json(['error' => 'Váratlan hiba történt: ' . $e->getMessage()], 500);
        }
    }

    public function jelentkezesLezaras($jelentkezo) {
        try {
            $jelentkezoId = $jelentkezo;
            
            $jelentkezesek = Jelentkezes::where('jelentkezo_id', $jelentkezoId)->get();
            
            if ($jelentkezesek->isEmpty()) {
                return response()->json(['message' => 'Nincsenek jelentkezések'], 200);
            }
            
            foreach ($jelentkezesek as $jelentkezes) {
                $jelentkezes->lezart = true;
                $jelentkezes->save();
            }
            
            return response()->json([
                'message' => 'Jelentkezések sikeresen lezárva',
                'jelentkezesek_szama' => $jelentkezesek->count()
            ], 200);
            
        } catch (\Exception $e) {
            Log::error('HIBA: ' . $e->getMessage());
            return response()->json(['error' => 'Váratlan hiba történt: ' . $e->getMessage()], 500);
        }
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

    public function getJelentkezesAllapot($email)
    {
        $jelentkezo = DB::table('jelentkezos')->where('email', $email)->first();

        $allapot = Jelentkezes::where('jelentkezo_id', $jelentkezo->id)
            ->join('allapotszotars', 'jelentkezes.allapot', '=', 'allapotszotars.id')
            ->select('allapotszotars.elnevezes', 'jelentkezes.jelentkezo_id')->first();

        return response()->json($allapot);
    }

    public function elfogadottakSzama()
    {
        $result = DB::table('jelentkezes as j')
        ->selectRaw("
            COUNT(*) as osszesen, 
            COUNT( CASE WHEN j.allapot = (Select id from allapotszotars where elnevezes='Elfogadva') THEN 1 END) as elfogadottak")
        ->join('szaks as sz', 'j.szak_id', '=', 'sz.id')
        ->join('allapotszotars as a', 'a.id', '=', 'j.allapot')
        ->get();



    return $result;
    }
    public function elfogadottakSzamaSzakonkent()
    {
        $result = DB::table('jelentkezes as j')
        ->selectRaw("
            sz.elnevezes,
            COUNT(*) as osszesen, 
            COUNT( CASE WHEN j.allapot = (Select id from allapotszotars where elnevezes='Elfogadva') THEN 1 END) as elfogadottak")
        ->join('szaks as sz', 'j.szak_id', '=', 'sz.id')
        ->join('allapotszotars as a', 'a.id', '=', 'j.allapot')
        ->groupBy('sz.elnevezes')
        ->get();

    return $result;
    }
    public function haviRegisztraciok(){

        // Jelentkezések száma havonta, szakonként
        $jelentkezesek = DB::table('jelentkezes')
        ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
        ->select(

            DB::raw('MONTH(jelentkezes.created_at) as honap'),
            DB::raw('count(jelentkezes.id) as jelentkezesek_szama')
        )
        ->whereRaw('YEAR(jelentkezes.created_at) = YEAR(NOW())')
        ->groupBy('honap')
        ->orderBy('honap')
        ->get();

        return $jelentkezesek;
    }
    
    public function haviRegisztraciokSzakonkent($szak){

        // Jelentkezések száma havonta, szakonként
        $jelentkezesek = DB::table('jelentkezes')
        ->join('szaks', 'jelentkezes.szak_id', '=', 'szaks.id')
        ->select(
            DB::raw('MONTH(jelentkezes.created_at) as honap'),
            DB::raw('count(jelentkezes.id) as jelentkezesek_szama')
        )
        ->where('szaks.id', $szak) // Szűrés a megadott szak ID-ra
        ->whereYear('jelentkezes.created_at', now()->year) // Szűrés az aktuális évre
        ->groupBy('honap')
        ->orderBy('honap')
        ->get();

        return $jelentkezesek;
    }
    
    public function archivalas(String $id){
        try {
            // Ellenőrizzük, hogy létezik-e a jelentkező az adott ID-val
            $jelentkezo = DB::table('jelentkezos')->find($id);

            if (!$jelentkezo) {
                return response()->json(['error' => 'Nem létező jelentkező'], 404);
            }

            // Lekérjük az "Archivált" állapot azonosítóját az allapotszotars táblából
            $archivaltAllapot = DB::table('allapotszotars')->where('elnevezes', 'Archivált')->value('id');

            if (!$archivaltAllapot) {
                return response()->json(['error' => 'Archivált állapot nem található'], 500);
            }

            // Frissítjük a jelentkezések állapotát az adott jelentkezőhöz
            DB::table('jelentkezes')
                ->where('jelentkezo_id', $id)
                ->update(['allapot' => $archivaltAllapot]);

            return response()->json(['message' => 'Jelentkezések archiválása sikeres']);
        } catch (\Exception $e) {
            Log::error('HIBA: ' . $e->getMessage());
            return response()->json(['error' => 'Váratlan hiba történt'], 500);
        }
    }

    public function jelentkezesEldontese($id, $ujAllapot) {        
        $jelentkezes = Jelentkezes::findOrFail($id);
    
        $oldAllapot = $jelentkezes->allapot;
        $ujAllapot = (int) $ujAllapot; 

        $jelentkezes->allapot = $ujAllapot;
        $jelentkezes->save();

        $frissitettJelentkezes = Jelentkezes::with(['allapotszotar'])->find($id);
        
        Statuszvaltozas::create([
            'jelentkezo_id' => $jelentkezes->jelentkezo_id,
            'szak_id'       => $jelentkezes->szak_id,
            'regi_allapot'  => $oldAllapot,
            'uj_allapot'    => $ujAllapot,
            'user_id'       => Auth::check() ? Auth::id() : null,
        ]);
        
        return response()->json([
            'message' => 'Jelentkezés állapota sikeresen frissítve.',
            'data' => $frissitettJelentkezes
        ]);
    }
}

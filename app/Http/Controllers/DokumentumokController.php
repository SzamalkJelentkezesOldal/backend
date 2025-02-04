<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DokumentumokFeltoltRequest;
use App\Models\Dokumentumok;
use App\Models\DokumentumTipus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumentumokController extends Controller
{
    public function nyilatkozatFeltolt(Request $request)
    {
        $request->validate([
            'ev' => 'required|integer|min:'.(date('Y')).'|max:'.(date('Y')+1),
            'nyilatkozat' => 'required|file|mimes:docx|max:5120'
        ]);

        $file = $request->file('nyilatkozat');
        $year = $request->ev;
        
        $path = $file->storeAs(
            "nyilatkozatok/{$year}", 
            "nyilatkozat_{$year}_v".(count(Storage::files("nyilatkozatok/{$year}")) + 1).".docx"
        );

        return response()->json([
            'message' => 'Fájl sikeresen feltöltve',
            'path' => Storage::url($path) 
        ]);
    }

    public function nyilatkozatLetolt($year)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Hozzáférés megtagadva'], 403);
            }

            if (!is_numeric($year) || $year < date('Y') || $year > date('Y')+1) {
                return response()->json(['error' => 'Érvénytelen év'], 422);
            }

            $directory = "nyilatkozatok/{$year}";
            $files = Storage::disk('private')->files($directory);

            Log::info($files);

            if (empty($files)) {
                return response()->json(['error' => 'Nincs dokumentum'], 404);
            }

            $latestFile = collect($files)
                ->sortByDesc(function ($file) {
                    $version = (int) Str::beforeLast(Str::afterLast($file, '_v'), '.docx');
                    return $version;
                })
                ->first();

                Log::info($latestFile);
            $fullPath = Storage::disk('private')->path($latestFile);

            return response()->download(
                $fullPath,
                "nyilatkozat_{$year}.docx",
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => 'attachment'
                ]
            );

        } catch (\Exception $e) {
            Log::error('Letöltési hiba: '.$e->getMessage());
            return response()->json(['error' => 'Szerverhiba: '.$e->getMessage()], 500);
        }
    }

    public function dokumentumokFeltolt(DokumentumokFeltoltRequest $request)
    {
        Log::info($request);
        $validated = $request->validated();
        $jelentkezoId = Auth::user()->jelentkezo->id;
    
        try {
            foreach ($validated as $field => $files) {
                $tipusNev = $this->getTipusNev($field);
                $tipus = DokumentumTipus::firstOrCreate(['elnevezes' => $tipusNev]);
                
                $paths = [];
                foreach ($files as $file) {
                    $paths[] = $file->store(
                        "dokumentumok/{$jelentkezoId}/{$tipus->id}", 
                        'private'
                    );
                }
    
                Dokumentumok::updateOrCreate(
                    [
                        'jelentkezo_id' => $jelentkezoId,
                        'dokumentum_tipus_id' => $tipus->id
                    ],
                    ['fajlok' => json_encode($paths)]
                );
            }
    
            return response()->json(['message' => 'Dokumentumok sikeresen mentve']);
    
        } catch (\Exception $e) {
            Log::error('Dokumentum feltöltési hiba: '.$e->getMessage());
            return response()->json([
                'error' => 'Szerverhiba: '.$e->getMessage()
            ], 500);
        }
    }

    public function getDokumentumok()
    {
        $user = Auth::user();
        if (!$user || !$user->jelentkezo) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dokumentumok = Dokumentumok::with('dokumentumTipus')
            ->where('jelentkezo_id', $user->jelentkezo->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$this->getTipusNev($item->dokumentumTipus->elnevezes) => json_decode($item->fajlok)];
            });

        return response()->json($dokumentumok);
    }


    private function getTipusNev($mezoNev)
    {
        $types = [
            'adoazonosito' => 'Adóigazolvány',
            'taj' => 'TAJ kártya',
            'szemelyi_elso' => 'Személyazonosító igazolvány első oldala',
            'szemelyi_hatso' => 'Személyazonosító igazolvány hátsó oldala',
            'lakcim_elso' => 'Lakcímet igazoló igazolvány első oldala',
            'lakcim_hatso' => 'Lakcímet igazoló igazolvány hátsó oldala',
            'onarckep' => 'Önarckép',
            'nyilatkozatok' => 'Nyilatkozazok',
            'erettsegik' => 'Érettségi bizonyítvány',
            'tanulmanyik' => 'Tanulmányi dokumentumok',
            'specialisok' => 'SNI/BTMN'
        ];
    
        return $types[$mezoNev] ?? 'Ismeretlen dokumentum típus';
    }
}

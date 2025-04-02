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
        $validated = $request->validated();
        $jelentkezoId = Auth::user()->jelentkezo->id;

       
        $requiredFields = [
            'adoazonosito', 'taj', 'szemelyi_elso', 'szemelyi_hatso',
            'lakcim_elso', 'lakcim_hatso', 'onarckep', 'nyilatkozatok'
        ];
        foreach ($requiredFields as $field) {
            $current = json_decode($request->input($field . '_current') ?? '[]', true);
           
            if (!$request->hasFile($field) && empty($current)) {
               
                return response()->json(['error' => "A {$field} fájl kötelező"], 422);
            }
        }

        try {
            $allFields = [
                "adoazonosito", "taj", "szemelyi_elso", "szemelyi_hatso",
                "lakcim_elso", "lakcim_hatso", "onarckep", "nyilatkozatok",
                "erettsegik", "tanulmanyik", "specialisok",
            ];

            foreach ($allFields as $field) {
               
                // beolvassuk a megtartott fájlokat
                $keptKey = $field . '_current';
                $keptFiles = $request->has($keptKey)
                    ? json_decode($request->input($keptKey), true)
                    : [];
             

                $tipusNev = $this->getTipusNev($field);
               
                $tipus = DokumentumTipus::firstOrCreate(['elnevezes' => $tipusNev]);
              

                $dokRecord = Dokumentumok::where('jelentkezo_id', $jelentkezoId)
                                ->where('dokumentum_tipus_id', $tipus->id)
                                ->first();
                
                $oldFiles = $dokRecord ? json_decode($dokRecord->fajlok, true) : [];
              
                $removedFiles = array_diff($oldFiles, $keptFiles);
              
                foreach ($removedFiles as $filePath) {
                    
                    Storage::disk('private')->delete($filePath);
                }

                $newPaths = [];
                if ($request->hasFile($field)) {
                    foreach ($request->file($field) as $file) {
                        $storedPath = $file->store("dokumentumok/{$jelentkezoId}/{$tipus->id}", 'private');
                        $newPaths[] = $storedPath;
                       
                    }
                }

                $finalPaths = array_merge($keptFiles, $newPaths);
              
                if (!empty($finalPaths)) {
                    Dokumentumok::updateOrCreate(
                        [
                            'jelentkezo_id' => $jelentkezoId,
                            'dokumentum_tipus_id' => $tipus->id
                        ],
                        ['fajlok' => json_encode($finalPaths)]
                    );
                } else {
                    if ($dokRecord) {
                        $dokRecord->delete();
                        Log::info("Deleted Dokumentum record for field", ['field' => $field]);
                    }
                }
            }

            return response()->json(['message' => 'Dokumentumok sikeresen mentve']);
        } catch (\Exception $e) {
            Log::error('Dokumentum feltöltési hiba: ' . $e->getMessage());
            return response()->json(['error' => 'Szerverhiba: ' . $e->getMessage()], 500);
        }
    }




    public function getDokumentumok()
    {
        $user = Auth::user();
        if (!$user || !$user->jelentkezo) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $dokumentumok = Dokumentumok::with('tipus')
            ->where('jelentkezo_id', $user->jelentkezo->id)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$this->getTipusAzonosito($item->tipus->elnevezes) => json_decode($item->fajlok)];
            });

        return response()->json($dokumentumok);
    }


    public function previewDokumentum(Request $request)
    {
        $path = $request->query('path');
        
        if (!Storage::disk('private')->exists($path)) {
            return response()->json(['error' => 'Fájl nem található'], 404);
        }
        
        return response()->file(Storage::disk('private')->path($path));
    }


    private function getTipusAzonosito($tipusNev)
    {
        $map = [
            'Adóigazolvány' => 'adoazonosito',
            'TAJ kártya' => 'taj',
            'Személyazonosító igazolvány első oldala' => 'szemelyi_elso',
            'Személyazonosító igazolvány hátsó oldala' => 'szemelyi_hatso',
            'Lakcímet igazoló igazolvány első oldala' => 'lakcim_elso',
            'Lakcímet igazoló igazolvány hátsó oldala' => 'lakcim_hatso',
            'Önarckép' => 'onarckep',
            'Nyilatkozatok' => 'nyilatkozatok',
            'Érettségi bizonyítvány' => 'erettsegik',
            'Tanulmányi dokumentumok' => 'tanulmanyik',
            'SNI/BTMN' => 'specialisok'
        ];
        
        return $map[$tipusNev] ?? 'Ismeretlen dokumentum típus';
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
            'nyilatkozatok' => 'Nyilatkozatok',
            'erettsegik' => 'Érettségi bizonyítvány',
            'tanulmanyik' => 'Tanulmányi dokumentumok',
            'specialisok' => 'SNI/BTMN'
        ];
    
        return $types[$mezoNev] ?? 'Ismeretlen dokumentum típus';
    }
}

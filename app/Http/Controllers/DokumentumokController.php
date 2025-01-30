<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
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
            'ev' => 'required|integer|min:2023|max:2025',
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

        if (!is_numeric($year) || $year < 2023 || $year > date('Y')+1) {
            return response()->json(['error' => 'Érvénytelen év'], 422);
        }

        $directory = "nyilatkozatok/{$year}";
        $files = Storage::disk('private')->files($directory);

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
}

<?php

namespace App\Http\Controllers;

use App\Models\JelentkezoTorzs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JelentkezoTorzsController extends Controller
{
    public function torzsadatFeltoltes(Request $request)
    {
        // Validáció
        $rules = [
            'jelentkezo_id' => 'required|integer|exists:jelentkezos,id',
            'vezeteknev' => 'required|string|max:255',
            'keresztnev' => 'required|string|max:255',
            'adoazonosito' => 'nullable|string|max:20|unique:jelentkezo_torzs,adoazonosito',
            'taj_szam' => 'nullable|string|max:20|unique:jelentkezo_torzs,taj_szam',
            'lakcim' => 'required|string|max:255',
            'szuletesi_hely' => 'required|string|max:255',
            'szuletesi_nev' => 'nullable|string|max:255',
            'szuletesi_datum' => 'required|date',
            'allampolgarsag' => 'required|string|max:255',
            'anyja_neve' => 'nullable|string|max:255',
        ];

        // Opcionális mezők dinamikus validációja
        $validator = Validator::make($request->all(), $rules);
        if ($request->allampolgarsag !== 'magyar') {
            $validator->sometimes(['adoazonosito', 'taj_szam'], 'nullable', function () {
                return true;
            });
        } else {
            $validator->sometimes(['adoazonosito', 'taj_szam'], 'required', function () {
                return true;
            });
        }

        // Validáció sikertelensége
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        // Adatok mentése az adatbázisba
        try {
            $data = $request->only([
                'jelentkezo_id',
                'vezeteknev',
                'keresztnev',
                'adoazonosito',
                'lakcim',
                'taj_szam',
                'szuletesi_hely',
                'szuletesi_nev',
                'szuletesi_datum',
                'allampolgarsag',
                'anyja_neve',
            ]);

            $torzs = JelentkezoTorzs::create($data);

            return response()->json([
                'message' => 'Az adatok sikeresen mentésre kerültek.',
                'data' => $torzs,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hiba történt az adatok mentése során.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
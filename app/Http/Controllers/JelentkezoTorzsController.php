<?php

namespace App\Http\Controllers;

use App\Models\Jelentkezo;
use App\Models\JelentkezoTorzs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JelentkezoTorzsController extends Controller
{
    public function torzsadatFeltoltes(Request $request)
    {
        // Validáció
        $rules = [
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



        try {
            $jelentkezo = Jelentkezo::where('email', $request->input('email'))->first();

            if (!$jelentkezo) {
                return response()->json([
                    'error' => 'A megadott email címhez nem található jelentkező.',
                ], 404);
            }

            $data = $request->except('email'); 
            $data['jelentkezo_id'] = $jelentkezo->id; 

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

    public function getJelentkezoAdatok($email) {
        $jelentkezo = DB::table('jelentkezos')->where('email', $email)->first();

        $adatok = JelentkezoTorzs::where('jelentkezo_id', $jelentkezo->id)->first();

        return response()->json($adatok);
    }

    public function updateJelentkezoTorzs(Request $request, $jelentkezo_id) {
        $rules = [
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

        $torzsAdat = JelentkezoTorzs::where('jelentkezo_id', $jelentkezo_id)->firstOrFail();
        $validated = $request->validate($rules);
        $torzsAdat->update($validated);
        
        return $torzsAdat;
      }
}
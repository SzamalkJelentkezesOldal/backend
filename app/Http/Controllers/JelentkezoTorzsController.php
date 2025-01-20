<?php

namespace App\Http\Controllers;

use App\Models\JelentkezoTorzs;
use Illuminate\Http\Request;

class JelentkezoTorzsController extends Controller
{
    public function torzsadatFeltoltes(Request $request)
    {
        $ellenorzottAdatok = $request->validate([
            'jelentkezo_id' => 'required|exists:jelentkezos,id',
            'vezeteknev' => 'required|string|max:255',
            'keresztnev' => 'required|string|max:255',
            'adoazonosito' => 'required|string|unique:jelentkezo,adoazonosito|max:20',
            'lakcim' => 'required|string|max:255',
            'taj_szam' => 'required|string|unique:jelentkezo,taj_szam|max:20',
            'szuletesi_hely' => 'required|string|max:255',
            'szuletesi_nev' => 'required|string|max:255',
            'szuletesi_datum' => 'required|date|before:today',
            'allampolgarsag' => 'required|string|max:255',
            'anyja_neve' => 'required|string|max:255',
            'szulo_elerhetoseg' => 'nullable|string|max:255',
        ], [
            'jelentkezo_id.required' => 'A jelentkező ID megadása kötelező.',
            'jelentkezo_id.exists' => 'A megadott jelentkező ID nem létezik.',
            'vezeteknev.required' => 'A vezetéknevet meg kell adni.',
            'vezeteknev.string' => 'A vezetéknevet karakterláncként kell megadni.',
            'vezeteknev.max' => 'A vezetékneved legfeljebb 255 karakter hosszú lehet.',
            'keresztnev.required' => 'A keresztnevet meg kell adni.',
            'keresztnev.string' => 'A keresztnevet karakterláncként kell megadni.',
            'keresztnev.max' => 'A keresztneved legfeljebb 255 karakter hosszú lehet.',
            'adoazonosito.required' => 'Az adóazonosító megadása kötelező.',
            'adoazonosito.unique' => 'Ez az adóazonosító már létezik.',
            'adoazonosito.max' => 'Az adóazonosító legfeljebb 20 karakter hosszú lehet.',
            'lakcim.required' => 'A lakcímet meg kell adni.',
            'lakcim.string' => 'A lakcím karakterláncként kell megadni.',
            'lakcim.max' => 'A lakcím legfeljebb 255 karakter hosszú lehet.',
            'taj_szam.required' => 'A TAJ szám megadása kötelező.',
            'taj_szam.unique' => 'Ez a TAJ szám már létezik.',
            'taj_szam.max' => 'A TAJ szám legfeljebb 20 karakter hosszú lehet.',
            'szuletesi_hely.required' => 'A születési hely megadása kötelező.',
            'szuletesi_hely.string' => 'A születési hely karakterláncként kell megadni.',
            'szuletesi_hely.max' => 'A születési hely legfeljebb 255 karakter hosszú lehet.',
            'szuletesi_nev.required' => 'A születési név megadása kötelező.',
            'szuletesi_nev.string' => 'A születési név karakterláncként kell megadni.',
            'szuletesi_nev.max' => 'A születési név legfeljebb 255 karakter hosszú lehet.',
            'szuletesi_datum.required' => 'A születési dátum megadása kötelező.',
            'szuletesi_datum.date' => 'A születési dátum formátuma érvénytelen.',
            'szuletesi_datum.before' => 'A születési dátumnak a mai nap előttinek kell lennie.',
            'allampolgarsag.required' => 'Az állampolgárság megadása kötelező.',
            'allampolgarsag.string' => 'Az állampolgárság karakterláncként kell megadni.',
            'allampolgarsag.max' => 'Az állampolgárság legfeljebb 255 karakter hosszú lehet.',
            'anyja_neve.required' => 'Az anyja neve megadása kötelező.',
            'anyja_neve.string' => 'Az anyja neve karakterláncként kell megadni.',
            'anyja_neve.max' => 'Az anyja neve legfeljebb 255 karakter hosszú lehet.',
            'szulo_elerhetoseg.string' => 'A szülő elérhetősége karakterláncként kell megadni.',
            'szulo_elerhetoseg.max' => 'A szülő elérhetősége legfeljebb 255 karakter hosszú lehet.',
        ]);
        try {
            $ujJelentkezo = JelentkezoTorzs::create([
                'jelentkezo_id' => $ellenorzottAdatok['jelentkezo_id'],
                'vezeteknev' => $ellenorzottAdatok['vezeteknev'],
                'keresztnev' => $ellenorzottAdatok['keresztnev'],
                'adoazonosito' => $ellenorzottAdatok['adoazonosito'],
                'lakcim' => $ellenorzottAdatok['lakcim'],
                'taj_szam' => $ellenorzottAdatok['taj_szam'],
                'szuletesi_hely' => $ellenorzottAdatok['szuletesi_hely'],
                'szuletesi_nev' => $ellenorzottAdatok['szuletesi_nev'],
                'szuletesi_datum' => $ellenorzottAdatok['szuletesi_datum'],
                'allampolgarsag' => $ellenorzottAdatok['allampolgarsag'],
                'anyja_neve' => $ellenorzottAdatok['anyja_neve'],
                'szulo_elerhetoseg' => $ellenorzottAdatok['szulo_elerhetoseg'] ?? null,
            ]);

            return response()->json($ujJelentkezo);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hiba történt az adatok mentése során.'], 422);
        }
    }
}
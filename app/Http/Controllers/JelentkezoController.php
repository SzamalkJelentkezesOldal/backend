<?php

namespace App\Http\Controllers;

use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JelentkezoController extends Controller
{
    public function postJelentkezoJelentkezesPortfolio(Request $request) {
        // Validációs szabályok
        $validation = Validator::make($request->all(), [
            'jelentkezo' => 'required|array',
            'jelentkezo.nev' => 'required|string|max:255',
            'jelentkezo.email' => 'required|email|max:255|unique:jelentkezos,email',
            'jelentkezo.tel' => 'required|string|max:15',
            'jelentkezes' => 'required|array',
            'jelentkezes.kivalasztottSzakok' => 'required|array|min:1',
            'jelentkezes.kivalasztottSzakok.*' => 'required|integer|exists:szaks,id',
            'portfolio' => 'array',
            'portfolio.images' => 'array',
            'portfolio.images.*' => 'string|max:255',
        ]);
    
        // Ha a validáció nem sikerül, válasz hibaüzenettel
        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);  // Validációs hiba válasz
        }
    
    
        try {
             // Jelentkezo felvétele
            $jelentkezo = new Jelentkezo();
            $jelentkezo->nev = $request->jelentkezo['nev'];
            $jelentkezo->email = $request->jelentkezo['email'];
            $jelentkezo->tel = $request->jelentkezo['tel'];
            $jelentkezo->save();

            // Jelentkezes mentése
            foreach ($request->jelentkezes['kivalasztottSzakok'] as $szakId) {
                // Jelentkezo táblába történő beszúrás
                $jelentkezes = new Jelentkezes();
                $jelentkezes->szak_id = $szakId;
                $jelentkezes->jelentkezo_id = $jelentkezo->id;
                $jelentkezes->allapot = 'eldöntésre vár'; // Kezdeti állapot
                $jelentkezes->save();
            }

            // Portfolió mentése
            foreach ($request->portfolio['images'] as $portfolio_url) {
                // Portfolio táblába történő beszúrás
                $portfolio = new Portfolio();
                $portfolio->jelentkezo_id = $jelentkezo->id;
                $portfolio->portfolio_url = $portfolio_url;
                $portfolio->save();
            }
        
            return response()->json([
                'message' => 'Jelentkezés sikeresen mentve.',
                'data' => $jelentkezo
            ], 201);  // sikeres válasz
        } catch (\Exception $e) {
           // Hibák kezelése
            return response()->json([
                'error' => 'Belső hiba történt: ' . $e->getMessage(),
            ], 500);
        }
    }
}

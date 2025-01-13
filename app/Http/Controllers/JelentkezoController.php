<?php

namespace App\Http\Controllers;

use App\Mail\JelentkezoMail;
use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
            'portfolio.portfolioSzakok' => 'nullable|array',
            'portfolio.portfolioSzakok.*.szak_id' => 'integer|exists:szaks,id',
            'portfolio.portfolioSzakok.*.portfolio_url' => 'url',

        ]);
    
        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422); 
        }
    
    
        try {
            $token = Str::random(40);

            // jelentkezobe
            $jelentkezo = new Jelentkezo();
            $jelentkezo->nev = $request->jelentkezo['nev'];
            $jelentkezo->email = $request->jelentkezo['email'];
            $jelentkezo->tel = $request->jelentkezo['tel'];
            $jelentkezo->token = $token;
            $jelentkezo->save();

            $regisztraciosLink = url("http://localhost:3000/register/{$token}");

            Mail::to($request->jelentkezo['email'])->send(new JelentkezoMail($request->jelentkezo['nev'], $regisztraciosLink));

            //jelentkezesbe
            foreach ($request->jelentkezes['kivalasztottSzakok'] as $szakId) {
                $jelentkezes = new Jelentkezes();
                $jelentkezes->szak_id = $szakId;
                $jelentkezes->jelentkezo_id = $jelentkezo->id;
                $jelentkezes->allapot = 'eldöntésre vár'; // Kezdeti állapot
                $jelentkezes->save();
            }

            // portfolioba
            if (!empty($request->portfolio['portfolioSzakok'])) {
                foreach ($request->portfolio['portfolioSzakok'] as $portfolioSzak) {
                    $portfolio = new Portfolio();
                    $portfolio->jelentkezo_id = $jelentkezo->id;
                    $portfolio->portfolio_url = $portfolioSzak['portfolio_url'];
                    $portfolio->szak_id = $portfolioSzak['szak_id'];
                    $portfolio->save();
                }
            }
        
            return response()->json([
                'message' => 'Jelentkezés sikeresen mentve.',
                'data' => $jelentkezo
            ], 201);  
        } catch (\Exception $e) {
            Log::error('Hiba történt: ' . $e->getMessage());
            return response()->json([
                'error' => 'Belső hiba történt: ' . $e->getMessage(),
            ], 500);
        }
    }
}

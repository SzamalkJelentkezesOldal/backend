<?php

namespace App\Http\Controllers;

use App\Mail\JelentkezoElutasitottMail;
use App\Mail\PortfolioEldontesMail;
use App\Models\Jelentkezo;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PortfolioController extends Controller
{
    public function portfolioOsszegzoEmail($portfolioId)
    {
        $portfolio = Portfolio::findOrFail($portfolioId);
        $jelentkezoId = $portfolio->jelentkezo_id;
    
        $portfoliok = Portfolio::with('szak')->where('jelentkezo_id', $jelentkezoId)->get();
    
        if ($portfoliok->contains(fn($pf) => $pf->allapot === 'Eldöntésre vár')) {
            return response()->json(['error' => 'Még vannak eldöntésre váró portfóliók.'], 422);
        }
    
        $acceptedPortfoliok = $portfoliok->where('allapot', 'Elfogadva');
        $rejectedPortfoliok = $portfoliok->where('allapot', 'Elutasítva');
    
        $jelentkezo = Jelentkezo::findOrFail($jelentkezoId);
    
        $registrationLink = url("http://localhost:3000/register/{$jelentkezo->token}");
    
        if ($acceptedPortfoliok->count() > 0) {
            
            Mail::to($jelentkezo->email)
                ->send(new PortfolioEldontesMail($jelentkezo, $acceptedPortfoliok, $rejectedPortfoliok, $registrationLink));
        } else {
            Mail::to($jelentkezo->email)
                ->send(new JelentkezoElutasitottMail($jelentkezo->nev));
        }
    
        return response()->json(['message' => 'Összegző email sikeresen elküldve.']);
    }

    public function updatePortfolio(Request $request, $portfolioId)
    {
        $request->validate([
            'allapot' => 'required|string|in:Eldöntésre vár,Elfogadva,Elutasítva'
        ]);

        $portfolio = Portfolio::findOrFail($portfolioId);
        $portfolio->allapot = $request->input('allapot');
        $portfolio->save();

        return response()->json([
            'message' => 'Portfólió állapot frissítve.',
            'portfolio' => $portfolio
        ]);
    }
}

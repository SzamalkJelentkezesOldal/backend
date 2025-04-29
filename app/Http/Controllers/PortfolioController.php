<?php

namespace App\Http\Controllers;

use App\Helpers\AllapotHelper;
use App\Mail\JelentkezoElutasitottMail;
use App\Mail\PortfolioEldontesMail;
use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use App\Models\Portfolio;
use App\Models\Statuszvaltozas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

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

        foreach ($portfoliok as $pf) {
            $pf->ertesito = true;
            $pf->save();
        }
    
        return response()->json(['message' => 'Összegző email sikeresen elküldve.']);
    }

    public function updatePortfolio(Request $request, $portfolioId)
    {
        // Validáld az új állapotot: elfogadott vagy elutasított
        $validator = Validator::make($request->all(), [
            'allapot' => 'required|string|in:Eldöntésre vár,Elfogadva,Elutasítva'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $newStatus = $request->input('allapot');

        // Keressük meg a portfóliót
        $portfolio = Portfolio::findOrFail($portfolioId);
        $oldPortfolioStatus = $portfolio->allapot;
        
        // Keressük meg a kapcsolódó jelentkezést
        $jelentkezes = Jelentkezes::where('jelentkezo_id', $portfolio->jelentkezo_id)
            ->where('szak_id', $portfolio->szak_id)
            ->first();
        if (!$jelentkezes) {
            return response()->json(['error' => 'Kapcsolódó jelentkezés nem található.'], 404);
        }
        
        if (AllapotHelper::hasStatusEvent($portfolio->jelentkezo_id, 'Regisztrált')) {
            return response()->json(['error' => 'A jelentkező már regisztrált, így nem módosítható a portfólió állapota.'], 422);
        }
        
        // Ha a portfólió elutasításra kerül
        if ($newStatus === 'Elutasítva') {
            // Frissítjük a portfóliót
            $portfolio->allapot = $newStatus;
            $portfolio->save();
            
            // Ha a portfólió elutasítása miatt a jelentkezésnek az állapota is elutasított kell legyen...
            $oldAppStatus = $jelentkezes->allapot;
            $jelentkezes->allapot = AllapotHelper::getId('Elutasítva');
            $jelentkezes->save();
            
            // Naplózzuk a változást
            Statuszvaltozas::create([
                'jelentkezo_id' => $portfolio->jelentkezo_id,
                'szak_id'       => $portfolio->szak_id,
                'regi_allapot'  => $oldAppStatus,
                'uj_allapot'    => $jelentkezes->allapot,
                'user_id'       => Auth::id() ?? null,
            ]);
        }
        // Ha a portfólió visszaáll elfogadottra
        else if ($newStatus === 'Elfogadva') {
            $portfolio->allapot = $newStatus;
            $portfolio->save();
            
            // Ha a jelentkezés eddig elutasított volt, de most visszaállítani szeretnénk,
            // az alkalmazás státuszát visszaállítjuk "Jelentkezett"-re.
            if ($jelentkezes->allapotszotar->elnevezes === 'Elutasítva') {
                $oldAppStatus = $jelentkezes->allapot;
                $jelentkezes->allapot = AllapotHelper::getId('Jelentkezett');
                $jelentkezes->save();
                
                // Naplózzuk a visszaállítást
                Statuszvaltozas::create([
                    'jelentkezo_id' => $portfolio->jelentkezo_id,
                    'szak_id'       => $portfolio->szak_id,
                    'regi_allapot'  => $oldAppStatus,
                    'uj_allapot'    => $jelentkezes->allapot,
                    'user_id'       => Auth::id() ?? null,
                ]);
            }
        }
        // Egyéb esetben (pl. ha az új státusz "Eldöntésre vár"), csak frissítjük a portfóliót
        else {
            $portfolio->allapot = $newStatus;
            $portfolio->save();
        }

        return response()->json([
            'message' => 'Portfólió állapot frissítve.',
            'portfolio' => $portfolio
        ]);
    }
}

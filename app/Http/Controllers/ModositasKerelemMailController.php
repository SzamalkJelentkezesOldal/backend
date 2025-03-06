<?php

namespace App\Http\Controllers;

use App\Helpers\AllapotHelper;
use App\Mail\ModositasKerelemMail;
use App\Models\Allapotszotar;
use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
use App\Models\Statuszvaltozas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ModositasKerelemMailController extends Controller
{
    public function emailKuldes(Request $request)
    {
        $data = $request->validate([
            'email'             => 'required|email',
            'torzsadatok'       => 'nullable|array',
            'dokumentumok'      => 'nullable|array',
            'selectedTorzsadat' => 'nullable|array',
            'selectedDokumentum'=> 'nullable|array',
        ]);

        // Küldd el az emailt a módosítási kérelemmel
        Mail::to($data['email'])->send(new ModositasKerelemMail($data));

        // Az "Eldöntésre vár" és "Módosításra vár" állapotok lekérdezése
        $eldontesreVarID = AllapotHelper::getId('Eldöntésre vár');
        $modositasraVarID = AllapotHelper::getId('Módosításra vár');

        if ($eldontesreVarID && $modositasraVarID) {
            // A jelentkező lekérdezése az email alapján
            $jelentkezo = Jelentkezo::where('email', $data['email'])->first();
            if ($jelentkezo) {
                // Csak azokat a jelentkezéseket módosítjuk, amelyek jelenlegi állapota "Eldöntésre vár"
                $jelentkezesek = Jelentkezes::where('jelentkezo_id', $jelentkezo->id)
                    ->where('allapot', $eldontesreVarID)
                    ->get();

                foreach ($jelentkezesek as $jelentkezes) {
                    // Az állapot frissítése "Módosításra vár"-ra
                    $jelentkezes->update(['allapot' => $modositasraVarID]);

                    // Opcióként rögzítheted a változást a statuszvaltozas táblában:
                    Statuszvaltozas::create([
                        'jelentkezo_id'  => $jelentkezo->id,
                        'szak_id'        => $jelentkezes->szak_id,
                        'regi_allapot'   => $eldontesreVarID,
                        'uj_allapot'     => $modositasraVarID,
                        'user_id'        => auth()->id() ?? null,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Email sikeresen elküldve, és az állapotok módosítva!']);
    }
}

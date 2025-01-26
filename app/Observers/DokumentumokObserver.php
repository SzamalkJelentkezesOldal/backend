<?php

namespace App\Observers;

use App\Helpers\AllapotHelper;
use App\Models\Dokumentumok;
use App\Models\Statuszvaltozas;
use Illuminate\Support\Facades\DB;

class DokumentumokObserver
{
    /**
     * Handle the Dokumentumok "created" event.
     */
    public function created(Dokumentumok $dokumentumok): void
    {
        DB::transaction(function () use ($dokumentumok) {
            $jelentkezo = $dokumentumok->jelentkezo;
    
            if ($jelentkezo) {
                foreach ($jelentkezo->jelentkezesek as $jelentkezes) {
                    $regiAllapot = $jelentkezes->allapot;

                    $ujAllapotId = AllapotHelper::getId("Dokumentumok feltöltve");
                    $jelentkezes->update(['allapot' => $ujAllapotId]);
    
                    Statuszvaltozas::create([
                        'jelentkezo_id' => $jelentkezo->id,
                        'szak_id' => $jelentkezes->szak_id,
                        'regi_allapot' => $regiAllapot,
                        'uj_allapot' => $ujAllapotId,
                        'user_id' => null,
                    ]);
                }
            }
        });
    }

    /**
     * Handle the Dokumentumok "updated" event.
     */
    public function updated(Dokumentumok $dokumentumok): void
    {
        //
    }

    /**
     * Handle the Dokumentumok "deleted" event.
     */
    public function deleted(Dokumentumok $dokumentumok): void
    {
        //
    }

    /**
     * Handle the Dokumentumok "restored" event.
     */
    public function restored(Dokumentumok $dokumentumok): void
    {
        //
    }

    /**
     * Handle the Dokumentumok "force deleted" event.
     */
    public function forceDeleted(Dokumentumok $dokumentumok): void
    {
        //
    }
}

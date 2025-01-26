<?php

namespace App\Observers;

use App\Enums\Allapot;
use App\Helpers\AllapotHelper;
use App\Models\Dokumentumok;
use App\Models\Jelentkezes;
use App\Models\Jelentkezo;
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
                    $ujAllapotId = AllapotHelper::getId(Allapot::DOKUMENTUMOK_FELTOLTVE);
                    $jelentkezes->update(['allapot' => $ujAllapotId]);
    
                    Statuszvaltozas::create([
                        'jelentkezo_id' => $jelentkezo->id,
                        'szak_id' => $jelentkezes->szak_id,
                        'allapot' => Allapot::DOKUMENTUMOK_FELTOLTVE->value,
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

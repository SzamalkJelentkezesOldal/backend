<?php

namespace App\Observers;

use App\Enums\Allapot;
use App\Helpers\AllapotHelper;
use App\Models\Jelentkezes;
use App\Models\JelentkezoTorzs;
use App\Models\Statuszvaltozas;
use Illuminate\Support\Facades\DB;

class JelentkezoTorzsObserver
{
    /**
     * Handle the JelentkezoTorzs "created" event.
     */
    public function created(JelentkezoTorzs $torzs): void
    {
        DB::transaction(function () use ($torzs) {
            $jelentkezesek = Jelentkezes::where('jelentkezo_id', $torzs->jelentkezo_id)->get();
    
            foreach ($jelentkezesek as $jelentkezes) {
                $ujAllapotId = AllapotHelper::getId(Allapot::TORZSADATOK_FELTOLTVE);
                $jelentkezes->update(['allapot' => $ujAllapotId]);
    
                Statuszvaltozas::create([
                    'jelentkezo_id' => $torzs->jelentkezo_id,
                    'szak_id' => $jelentkezes->szak_id,
                    'allapot' => Allapot::TORZSADATOK_FELTOLTVE->value,
                    'user_id' => null,
                ]);
            }
        });
    }

    /**
     * Handle the JelentkezoTorzs "updated" event.
     */
    public function updated(JelentkezoTorzs $jelentkezoTorzs): void
    {
        //
    }

    /**
     * Handle the JelentkezoTorzs "deleted" event.
     */
    public function deleted(JelentkezoTorzs $jelentkezoTorzs): void
    {
        //
    }

    /**
     * Handle the JelentkezoTorzs "restored" event.
     */
    public function restored(JelentkezoTorzs $jelentkezoTorzs): void
    {
        //
    }

    /**
     * Handle the JelentkezoTorzs "force deleted" event.
     */
    public function forceDeleted(JelentkezoTorzs $jelentkezoTorzs): void
    {
        //
    }
}

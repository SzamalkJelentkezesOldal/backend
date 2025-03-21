<?php

namespace App\Observers;

use App\Helpers\AllapotHelper;
use App\Models\Jelentkezes;
use App\Models\JelentkezoTorzs;
use App\Models\Statuszvaltozas;
use Illuminate\Support\Carbon;
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
                $regiAllapot = $jelentkezes->allapot;

                $ujAllapotId = AllapotHelper::getId("Törzsadatok feltöltve");
                $jelentkezes->update(['allapot' => $ujAllapotId]);
    
                Statuszvaltozas::create([
                    'jelentkezo_id' => $torzs->jelentkezo_id,
                    'szak_id' => $jelentkezes->szak_id,
                    'regi_allapot' => $regiAllapot,
                    'uj_allapot' => $ujAllapotId,
                    'user_id' => null,
                ]);
            }
        });
    }
    public function saved(JelentkezoTorzs $jelentkezo)
    {
        $szeptemberElso = Carbon::now()->year . '-09-01';
        $elutasitvaStatuszId = AllapotHelper::getId("Elutasítva");

        $szuletesiDatum = Carbon::parse($jelentkezo->szuletesi_datum);
        if ($szuletesiDatum->addYears(25)->lt(Carbon::parse($szeptemberElso))) {
            DB::table('jelentkezes as j')
                ->join('szaks as sz', 'sz.id', '=', 'j.szak_id')
                ->where('jelentkezo_id', $jelentkezo->jelentkezo_id)
                ->where('sz.nappali', true)
                ->update(['j.allapot' => $elutasitvaStatuszId]);
        }
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

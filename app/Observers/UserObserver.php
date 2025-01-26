<?php

namespace App\Observers;

use App\Helpers\AllapotHelper;
use App\Models\Jelentkezo;
use App\Models\Statuszvaltozas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        DB::transaction(function () use ($user) {
            $jelentkezo = Jelentkezo::where('email', $user->email)->first();
    
            if ($jelentkezo) {
                foreach ($jelentkezo->jelentkezesek as $jelentkezes) {
                    $regiAllapot = $jelentkezes->allapot;

                    $ujAllapotId = AllapotHelper::getId("Regisztrált");
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
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

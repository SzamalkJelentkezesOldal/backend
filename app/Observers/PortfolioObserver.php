<?php

namespace App\Observers;

use App\Mail\JelentkezoElutasitottMail;
use App\Mail\JelentkezoMail;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Mail;

class PortfolioObserver
{
    /**
     * Handle the Portfolio "created" event.
     */
    public function created(Portfolio $portfolio): void
    {
        if ($portfolio->wasChanged('allapot')) {
            if ($portfolio->allapot === 'Elfogadva') {
                $regisztraciosLink = url("http://localhost:3000/register/{$portfolio->jelentkezo->token}");

                Mail::to($portfolio->jelentkezo->email)
                    ->send(new JelentkezoMail($portfolio->jelentkezo->nev, $regisztraciosLink));
            }
            elseif ($portfolio->allapot === 'Elutasítva') {
                Mail::to($portfolio->jelentkezo->email)
                    ->send(new JelentkezoElutasitottMail($portfolio->jelentkezo->nev));
            }
        }
    }

    /**
     * Handle the Portfolio "updated" event.
     */
    public function updated(Portfolio $portfolio): void
    {
        //
    }

    /**
     * Handle the Portfolio "deleted" event.
     */
    public function deleted(Portfolio $portfolio): void
    {
        //
    }

    /**
     * Handle the Portfolio "restored" event.
     */
    public function restored(Portfolio $portfolio): void
    {
        //
    }

    /**
     * Handle the Portfolio "force deleted" event.
     */
    public function forceDeleted(Portfolio $portfolio): void
    {
        //
    }
}

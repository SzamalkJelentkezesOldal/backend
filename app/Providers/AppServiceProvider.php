<?php

namespace App\Providers;

use App\Models\Dokumentumok;
use App\Models\JelentkezoTorzs;
use App\Models\User;
use App\Observers\DokumentumokObserver;
use App\Observers\JelentkezoTorzsObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JelentkezoTorzs::observe(JelentkezoTorzsObserver::class);
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
        if (!app()->runningInConsole()) {
            User::observe(UserObserver::class);
            JelentkezoTorzs::observe(JelentkezoTorzsObserver::class);
            Dokumentumok::observe(DokumentumokObserver::class);
        }
    }
}

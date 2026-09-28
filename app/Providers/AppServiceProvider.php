<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Formulaires publics (contact, devis, commentaires, newsletter) : limite par IP uniquement.
        // Inclure l'email dans la clé permettait de contourner la limite en changeant d'adresse.
        RateLimiter::for('contact-form', function (Request $request) {
            return [
                Limit::perMinute(5)->by('minute|'.$request->ip()),
                Limit::perHour(20)->by('hour|'.$request->ip()),
            ];
        });
    }
}

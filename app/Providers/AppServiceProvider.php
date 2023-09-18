<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
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
        Http::macro('weather', function () {
            return Http::baseUrl('https://api.openweathermap.org/data/2.5/weather');
        });

        Http::macro('pokeapi', function () {
            return Http::baseUrl('https://pokeapi.co/api/v2');
        });

    }

}

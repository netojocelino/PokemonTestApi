<?php

namespace App\Http\Controllers;

use App\Services\PokemonService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AppController extends Controller
{

    protected WeatherService $weather;
    protected PokemonService $pokeApi;

    public function __construct()
    {
        $this->weather = new WeatherService(config('app.keys.weather'));
        $this->pokeApi = new PokemonService;
    }

    public function api(Request $request)
    {
        $city = $request->query('city');

        $cityData = Cache::remember(
            $city, 60 * 5, fn () => $this->weather->handle($city)
        );

        $type = $this->pokeApi->getTypeByTemperature($cityData);
        $pokemon = Cache::remember(
            $type, 60 * 5, fn() => $this->pokeApi->getPokemonByType($type)
        );

        $pokemon = $pokemon[random_int(0, count($pokemon) - 1)]['pokemon'];

        $pokemonData = Cache::remember(
            $pokemon['name'], 60 * 5, fn() => $this->pokeApi->getPokemon($pokemon['name'])
        );

        return response()->json([
            'city'      => $city,
            'cityData'  => $cityData,
            'pokemon'   => [
                'id'      => data_get($pokemonData, 'id'),
                'forms'   => data_get($pokemonData, 'forms'),
                'sprites' => [
                    'front_default' => data_get($pokemonData, 'sprites.other.official-artwork.front_default'),
                    'front_shiny'   => data_get($pokemonData, 'sprites.other.official-artwork.front_shiny'),
                ],
                'types'  => data_get($pokemonData, 'types'),
                'weight' => data_get($pokemonData, 'weight'),
            ],
        ]);
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PokemonService
{
    protected mixed $client;

    public function handle()
    {
        $request = Http::pokeapi()
            ->get('/')
            ->getBody()
            ->getContents();

        $result = json_decode($request, true);

        return $result;
    }


    public function getTypeByTemperature(array $data)
    {
        if ($data['is_raining']) return 'electric';

        if ($data['temperature'] < 5) return 'ice';
        if ($data['temperature'] < 10) return 'water';
        if ($data['temperature'] < 15) return 'grass';
        if ($data['temperature'] < 20) return 'ground';
        if ($data['temperature'] < 25) return 'bug';
        if ($data['temperature'] < 30) return 'rock';

        return 'normal';
    }

    public function getPokemonByType(string $type, bool $unique = false)
    {
        $request = Http::pokeapi()
            ->get('/type/'.$type)
            ->getBody()
            ->getContents();

        $result = json_decode($request, true)['pokemon'];

        if ($unique) return $result[ random_int(0, count($result)) ];

        return $result;
    }

    /** @param int|string $info */
    public function getPokemon(mixed $info)
    {
        $request = Http::pokeapi()
            ->get('/pokemon/'.$info)
            ->getBody()
            ->getContents();
        $result = json_decode($request, true);

        return $result;
    }

}

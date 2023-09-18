<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    protected string $apiKey;
    protected mixed $client;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function handle(string $city)
    {
        $request = Http::weather()
            ->get('/', [
                'q'     => $city,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ])
            ->getBody()
            ->getContents();

        $result = json_decode($request, true);
        $response = [
            'weathers'    => array_column($result['weather'], 'main'),
            'temperature' => $result['main']['temp'],
            'name'        => $result['name'],
        ];
        $response['is_raining'] = in_array('rain', $response['weathers']);

        return $response;
    }
}

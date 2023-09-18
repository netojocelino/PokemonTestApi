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

        return $this->setResponse($result);
    }

    public function setResponse(array $data)
    {
        if ($data['cod'] != '200') {
            return $data;
        }

        $response = [
            'weathers'    => array_column($data['weather'], 'main'),
            'temperature' => $data['main']['temp'],
            'name'        => $data['name'],
        ];
        $response['is_raining'] = in_array('rain', $response['weathers']);

        return $response;
    }
}

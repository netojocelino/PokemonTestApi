<?php

namespace Tests\Unit;

use App\Services\WeatherService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class WeatherServiceTest extends TestCase
{
    use CreatesApplication;

    public function test_service_must_returns_valid_data_from_remote_api(): void
    {
        $app = new WeatherService(getenv('WEATHER_KEY'));

        $data = $app->handle('Nossa Senhora da Glória');

        $this->assertNotNull($data['weathers']);
        $this->assertIsArray($data['weathers']);
        $this->assertIsNumeric($data['temperature']);
        $this->assertNotNull($data['name']);
        $this->assertIsBool($data['is_raining']);
    }

    public function test_service_must_returns_not_found_data_from_remote_api(): void
    {

        $app = new WeatherService(getenv('WEATHER_KEY'));
        $data = $app->handle('Not Found City');

        $this->assertNull(data_get($data,'weathers'));
        $this->assertNotNull($data['cod']);
        $this->assertEquals($data['cod'], '404');
        $this->assertNotNull($data['message']);
        $this->assertEquals($data['message'], 'city not found');
    }

    public function test_service_must_returns_valid_data_from_mock_api(): void
    {
        $url = "https://api.openweathermap.org/data/2.5/weather";
        $apiKey = getenv('WEATHER_KEY');
        $body = json_decode(file_get_contents(base_path('tests/files/nossa-senhora-da-gloria.weather.json')), true);
        Http::preventStrayRequests();
        Http::fake([ $url.'/*' => Http::response($body, 200), ]);

        $app = new WeatherService($apiKey);
        $response = json_decode(Http::get("$url/", [
            'q'     => 'Nossa Senhora da Glória',
            'appid' => $apiKey,
            'units' => 'metric',
        ]), true);
        $data = $app->setResponse($response);

        $this->assertNotNull($data['weathers']);
        $this->assertIsArray($data['weathers']);
        $this->assertIsNumeric($data['temperature']);
        $this->assertNotNull($data['name']);
        $this->assertIsBool($data['is_raining']);
        $this->assertEquals($data['weathers'], array_column($body['weather'], 'main'));
        $this->assertEquals($data['temperature'], $body['main']['temp']);
        $this->assertEquals($data['name'], $body['name']);
        $this->assertEquals($data['is_raining'], in_array('rain', array_column($body['weather'], 'main')));
    }

    public function test_service_must_returns_not_found_data_from_mock_api(): void
    {
        $url = "https://api.openweathermap.org/data/2.5/weather";
        $apiKey = getenv('WEATHER_KEY');
        $body = json_decode(file_get_contents(base_path('tests/files/not-found.weather.json')), true);
        Http::preventStrayRequests();
        Http::fake([ $url.'/*' => Http::response($body, 200), ]);

        $app = new WeatherService($apiKey);
        $response = json_decode(Http::get("$url/", [
            'q'     => 'Nossa Senhora da Glória',
            'appid' => $apiKey,
            'units' => 'metric',
        ]), true);
        $data = $app->setResponse($response);


        $this->assertNull(data_get($data,'weathers'));
        $this->assertNotNull($data['cod']);
        $this->assertEquals($data['cod'], '404');
        $this->assertNotNull($data['message']);
        $this->assertEquals($data['message'], 'city not found');
    }

}

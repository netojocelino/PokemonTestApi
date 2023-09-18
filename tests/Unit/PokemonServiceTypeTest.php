<?php

namespace Tests\Unit;

use App\Services\PokemonService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class PokemonServiceTypeTest extends TestCase
{
    use CreatesApplication;


    public function test_service_must_returns_ice_from_remote_api_by_low_temperature(): void
    {
        $app = new PokemonService();
        $data = [
            'is_raining'  => false,
            'temperature' => 4,
        ];

        $response = $app->getTypeByTemperature($data);

        $this->assertEquals($response, 'ice');
    }

    public function test_service_must_returns_ice_from_remote_api_when_raining(): void
    {
        $app = new PokemonService();
        $data = [
            'is_raining'  => true,
            'temperature' => 4,
        ];

        $response = $app->getTypeByTemperature($data);

        $this->assertEquals($response, 'electric');
    }

    public function test_service_must_returns_normal_from_remote_api_wnot_hen_raining_and_too_hot(): void
    {
        $app = new PokemonService();
        $data = [
            'is_raining'  => false,
            'temperature' => 40,
        ];

        $response = $app->getTypeByTemperature($data);

        $this->assertEquals($response, 'normal');
    }

 }

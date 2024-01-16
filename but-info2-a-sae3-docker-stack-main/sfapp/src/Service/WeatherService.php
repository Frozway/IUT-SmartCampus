<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;



class WeatherService
{
    

    public function getWeather(HttpClientInterface $httpClient, string $city): JsonResponse
    {
        $APIKey = 'bef0f873bd6633be3fbd81bedb9a02be';

        $response = $httpClient->request('GET', "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$APIKey}&lang=fr");
        $weatherData = $response->toArray();

        return new JsonResponse($weatherData);
    }
}


?>
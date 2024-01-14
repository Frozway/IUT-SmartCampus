<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiController extends AbstractController
{
    #[Route('/api/getLastValues', name: 'api_get_last_values')]
    public function index(ManagerRegistry $doctrine, HttpClientInterface $httpClient): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $json = file_get_contents('json/database.json');
        $json_data = json_decode($json, true);

        $values = [];

        foreach ($json_data as $key => $value) {
            try {
                $tempResponse = $httpClient->request('GET', "https://sae34.k8s.iut-larochelle.fr/api/captures/last", [
                    'headers' => [
                        'accept' => 'application/ld+json',
                        'dbname' => $value['dbname'],
                        'username' => 'k1eq3',
                        'userpass' => 'wohtuh-nigzup-diwhE4',
                    ],
                    'query' => [
                        'limit' => 1,
                        'nom' => 'temp',
                    ],
                ]);

                // Requête pour l'humidité
                $humidityResponse = $httpClient->request('GET', "https://sae34.k8s.iut-larochelle.fr/api/captures/last", [
                    'headers' => [
                        'accept' => 'application/ld+json',
                        'dbname' => $value['dbname'],
                        'username' => 'k1eq3',
                        'userpass' => 'wohtuh-nigzup-diwhE4',
                    ],
                    'query' => [
                        'limit' => 1,
                        'nom' => 'hum',
                    ],
                ]);

                // Requête pour le CO2
                $co2Response = $httpClient->request('GET', "https://sae34.k8s.iut-larochelle.fr/api/captures/last", [
                    'headers' => [
                        'accept' => 'application/ld+json',
                        'dbname' => $value['dbname'],
                        'username' => 'k1eq3',
                        'userpass' => 'wohtuh-nigzup-diwhE4',
                    ],
                    'query' => [
                        'limit' => 1,
                        'nom' => 'co2',
                    ],
                ]);

                // Récupération des données de chaque réponse
                $tempData = $tempResponse->toArray();
                $humidityData = $humidityResponse->toArray();
                $co2Data = $co2Response->toArray();
                $room = $roomRepository->findOneBy(['name' => $value['room']]);

                if (!$room) {
                    continue;
                }

                $roomId = $room->getId();

                // Intégration des données dans le tableau $values
                $values[$value['room']] = [
                    'temp' => $tempData[0],
                    'humidity' => $humidityData[0],
                    'co2' => $co2Data[0],
                    'roomID' => $roomId,
                ];
            } catch (\Exception $e) {
                $values[$value['room']] = [
                    'temp' => [
                        'value' => 'N/A',
                    ],
                    'humidity' => [
                        'value' => 'N/A',
                    ],
                    'co2' => [
                        'value' => 'N/A',
                    ],
                ];
            }
        }
        return $this->json($values);
    }

    #[Route('/api/getWeather', name: 'api_get_weather')]
    public function getWeather(RequestStack $requestStack, HttpClientInterface $httpClient): JsonResponse
    {
        $request = $requestStack->getCurrentRequest();
        $city = $request->query->get('city');

        $APIKey = 'bef0f873bd6633be3fbd81bedb9a02be';

        $response = $httpClient->request('GET', "https://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$APIKey}&lang=fr");

        $weatherData = $response->toArray();

        return $this->json($weatherData);
    }
}

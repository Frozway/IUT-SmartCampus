<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    #[Route('/api/getLastValues', name: 'api_get_last_values')]
    public function index(HttpClientInterface $httpClient): JsonResponse
    {
        $json = file_get_contents('json/database.json');
        $json_data = json_decode($json, true);

        $values = [];

        foreach ($json_data as $key => $value) {
            // envoyer une requête à l'api pour chaque $value['room']
            // on ajoute les valeurs à $values
            // $values = json_encode([...$values, ...$response]);
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

                // Intégration des données dans le tableau $values
                $values[$value['room']] = [
                    'temp' => $tempData[0],
                    'humidity' => $humidityData[0],
                    'co2' => $co2Data[0],
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
}

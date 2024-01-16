<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class DefaultDashboardTest extends WebTestCase
{

    public function testAccessPage(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $client->request('GET', '/');


        // ************************* TEST **************************** \\

        $this->assertResponseIsSuccessful();
    }

    public function testBlockTitle(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $client->request('GET', '/');


        // ************************* TEST **************************** \\

        $this->assertSelectorTextContains('head title', 'Acceuil | SmartCampus');
    }

    public function testUserNavbar(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $client->request('GET', '/');


        // ************************* TEST **************************** \\

        // Seule la navbar d'un utilisateur non connecté contient l'élément "Login"
        $this->assertSelectorExists('div.navbar a img[alt="Login"]');
    }

    public function testUserRoomList(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $crawler= $client->request('GET', '/');

        // Récupérer le nombre de salles dans la base de données
        $numberOfRooms = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->countRooms();


        // ************************* TEST **************************** \\

        // Seule la liste d'un utilisateur non connecté n'a pas le bouton "ajouter" contenu dans une div avec id="AddRoomAdmin"
        $this->assertSelectorNotExists('div#AddRoomAdmin button');

        // Cette liste des salles contient bien le texte Salles dans une div
        $this->assertSelectorTextContains('div', 'Salles');

        // Cette liste contient bien autant de li que de salle dans la base de données
        $this->assertEquals($numberOfRooms, $crawler->filter('li')->count());
    }

    public function testUserRoomDetailsLink(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $crawler = $client->request('GET', '/');

        // Récupérer l'id de la première salle
        $firstRoomId = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->getFirstRoomId();


        // ************************* TEST **************************** \\

        // Récupérer le lien de la première salle
        $link = $crawler->filter('ul#RoomList a#Room')->first();

        // Vérifier que le lien existe
        $this->assertNotEmpty($link);

        // Vérifier que le lien est bien celui qui redirige vers la page de détails de la première salle
        $this->assertEquals('/room/' . $firstRoomId, $link->attr('href'));

        // Cliquer sur la salle pour accéder à la page de détails de la salle
        $crawler= $client->request('GET', '/room/' . $firstRoomId);

        // Vérifier que les détails de la salle sont bien affichés
        $this->assertSelectorExists('div#UserRoomDetails');
    }

    public function testEmptyDataHandling(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $client->request('GET', '/');

        // Récupérer l'id de la première salle
        $firstRoomId = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->getFirstRoomId();

        // Effectuez la requête GET vers la page
        $client->request('GET', '/room/' . $firstRoomId);


        // ************************* TEST **************************** \\

        // On sait que l'appel à l'API n'étant pas possible depuis ces tests
        // On vérifie alors que la page informe bien l'utilisateur qu'aucune donnée n'est disponible
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-info', 'Aucune donnée à afficher');
    }

    public function testChartDataCreation(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $client->request('GET', '/');

        // Récupérer l'id de la première salle
        $firstRoomId = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->getFirstRoomId();

        $client->request('GET', '/room/' . $firstRoomId);


        // ************************* TEST **************************** \\

        // On vérifie que les graphiques sont bien créés dans la page
        $this->assertSelectorExists('#temperatureChart');
        $this->assertSelectorExists('#co2Chart');
        $this->assertSelectorExists('#humidityChart');
    }

    public function testDataLimit864(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page d'accueil hors connexion
        $client->request('GET', '/');

        // Récupérer l'id de la première salle
        $firstRoomId = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->getFirstRoomId();

        $client->request('GET', '/room/' . $firstRoomId, ['dataLimit' => 864]);


        // ************************* TEST **************************** \\

        // Assertions spécifiques pour dataLimit = 864
        $this->assertResponseIsSuccessful();
    }
}

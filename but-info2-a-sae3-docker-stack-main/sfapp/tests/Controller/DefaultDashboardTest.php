<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class DefaultDashboardTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->numberOfRooms = $this->client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->countRooms();
        $this->firstRoomId = $this->client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->getFirstRoomId();
    }

    public function testAccessPage(): void
    {
        $this->crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }

    public function testBlockTitle(): void
    {
        $this->crawler = $this->client->request('GET', '/');

        $this->assertSelectorTextContains('head title', 'Acceuil | SmartCampus');
    }

    public function testUserNavbar(): void
    {
        $this->crawler = $this->client->request('GET', '/');

        // Seule la navbar d'un utilisateur non connecté contient l'élément "Login"
        $this->assertSelectorExists('div.navbar a img[alt="Login"]');
    }

    public function testUserRoomList(): void
    {
        $this->crawler = $this->client->request('GET', '/');

        // Seule la liste d'un utilisateur non connecté n'a pas le bouton "ajouter" contenu dans une div avec id="AddRoomAdmin"
        $this->assertSelectorNotExists('div#AddRoomAdmin button');

        // Cette liste des salles contient bien le texte Salles dans une div
        $this->assertSelectorTextContains('div', 'Salles');

        // Cette liste contient bien autant de li que de salle dans la base de données
        $this->assertEquals($this->numberOfRooms, $this->crawler->filter('li')->count());
    }

    public function testUserRoomDetailsLink(): void
    {
        $this->crawler = $this->client->request('GET', '/');

        // Récupérer le lien de la première salle
        $link = $this->crawler->filter('ul#RoomList a#Room')->first();

        // Vérifier que le lien existe
        $this->assertNotEmpty($link);

        // Vérifier que le lien est bien celui qui redirige vers la page de détails de la première salle
        $this->assertEquals('/room/' . $this->firstRoomId, $link->attr('href'));

        // Cliquer sur la salle pour accéder à la page de détails de la salle
        $this->client->click($link->link());

        // Vérifier que les détails de la salle sont bien affichés
        $this->assertSelectorExists('div#UserRoomDetails');
    }

    public function testEmptyDataHandling(): void
    {
        // Créez un mock pour HttpClientInterface pour simuler un appel à l'API vide
        $mockHttpClient = new MockHttpClient([new MockResponse('{}')]);

        // Utilisez le mock à la place du service HttpClientInterface réel
        $container = $this->client->getContainer();
        $container->set('http_client', $mockHttpClient);

        // Effectuez la requête GET vers la page
        $this->client->request('GET', '/room/' . $this->firstRoomId);

        // Assertions - Vérifiez que la page affiche un message informatif lorsque data est vide
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert-info', 'Aucune donnée à afficher');
    }

    public function testChartDataCreation(): void
    {
        $this->client->request('GET', '/room/' . $this->firstRoomId);

        // Vérifiez le comportement du composant roomDataCharts lors de la création des graphiques
        $this->assertSelectorExists('#temperatureChart');
        $this->assertSelectorExists('#co2Chart');
        $this->assertSelectorExists('#humidityChart');
    }

    public function testDataLimit864(): void
    {
        $this->client->request('GET', '/room/' . $this->firstRoomId, ['dataLimit' => 864]);

        // Assertions spécifiques pour dataLimit = 864
        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Libérer les ressources si nécessaire
        $this->client = null;
        $this->crawler = null;
        $this->numberOfRooms = null;
    }
}

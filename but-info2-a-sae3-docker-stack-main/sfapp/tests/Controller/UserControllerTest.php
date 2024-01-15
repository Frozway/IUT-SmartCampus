<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $crawler;
    private $firstRoomId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', '/');
        $this->numberOfRooms = $this->client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->countRooms();
        $this->firstRoomId = $this->client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->getFirstRoomId();
    }

    public function testAccessPage(): void
    {
        $this->assertResponseIsSuccessful();
    }

    public function testBlockTitle(): void
    {
        $this->assertSelectorTextContains('head title', 'Acceuil | SmartCampus');
    }

    public function testUserNavbar(): void
    {
        // Seule la navbar d'un utilisateur non connecté contient l'élément "Login"
        $this->assertSelectorExists('div.navbar a img[alt="Login"]');
    }

    public function testUserRoomList(): void
    {
        // Seule la liste d'un utilisateur non connecté n'a pas le bouton "ajouter" contenu dans une div avec id="AddRoomAdmin"
        $this->assertSelectorNotExists('div#AddRoomAdmin button');

        // Cette liste des salles contient bien le texte Salles dans une div
        $this->assertSelectorTextContains('div', 'Salles');

        // Cette liste contient bien autant de li que de salle dans la base de données
        $this->assertEquals($this->numberOfRooms, $this->crawler->filter('li')->count());
    }

    public function testUserRoomDetailsLink(): void
    {
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

    protected function tearDown(): void
    {
        parent::tearDown();

        // Libérer les ressources si nécessaire
        $this->client = null;
        $this->crawler = null;
        $this->numberOfRooms = null;
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $this->assertSelectorTextContains('div#list-title', 'Salles');

        // Cette liste contient bien autant de li que de salle dans la base de données
        $this->assertEquals($numberOfRooms, $crawler->filter('li')->count());
    }
}

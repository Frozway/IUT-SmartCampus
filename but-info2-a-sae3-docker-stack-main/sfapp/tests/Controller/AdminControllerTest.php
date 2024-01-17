<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testAccessPage(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $admin = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findAdminUser();
        $client->loginUser($admin);

        // Accéder à la page principale du technicien
        $client->request('GET', '/admin-dashboard');


        // ************************* TEST **************************** \\

        $this->assertResponseIsSuccessful();
    }

    public function testBlockTitle(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $admin = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findAdminUser();
        $client->loginUser($admin);

        // Accéder à la page principale du technicien
        $client->request('GET', '/admin-dashboard');


        // ************************* TEST **************************** \\

        $this->assertSelectorTextContains('head title', 'Admin Dashboard');

        // Vérifier aussi que le titre visible sur la page est bien Tableau de bord
        $this->assertSelectorTextContains('span.dashboard-title', 'Tableau de bord - Chargé de Mission');
    }

    public function testAdminNavbar(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $admin = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findAdminUser();
        $client->loginUser($admin);

        // Accéder à la page principale du technicien
        $client->request('GET', '/admin-dashboard');


        // ************************* TEST **************************** \\

        // Seule la navbar d'un utilisateur connecté contient l'élément "Logout"
        $this->assertSelectorExists('div.navbar a img[alt="Logout"]');
    }

    public function testAcquisitionSystemListForAdmin(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $admin = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findAdminUser();
        $client->loginUser($admin);

        // Accéder à la page principale du technicien
        $client->request('GET', '/admin-dashboard');


        // ************************* TEST **************************** \\

        //Seule la liste des AS de l'admin à le titre de "Systèmes d'aquisition"
        $this->assertSelectorTextContains('div#AS-Title', "Systèmes d'acquisition");

        //Seule la liste des AS de l'admin à un bouton ajouter
        $this->assertSelectorExists('div#add-as-button');
    }

    public function testAdminRoomList(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $admin = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findAdminUser();
        $client->loginUser($admin);

        // Accéder à la page principale du technicien
        $crawler = $client->request('GET', '/admin-dashboard');

        // Récupérer le nombre de salles dans la base de données
        $numberOfRooms = $client->getContainer()->get('doctrine')->getRepository('App\Entity\Room')->countRooms();


        // ************************* TEST **************************** \\

        // Seule la liste d'un utilisateur connecté en admin a le bouton "ajouter" contenu dans une div avec id="AddRoomAdmin"
        $this->assertSelectorExists('div#AddRoomAdmin');

        // Cette liste des salles contient bien le texte Salles dans une div
        $this->assertSelectorTextContains('div#list-title', 'Salles');

        // Cette liste contient bien autant de li que de salle dans la base de données
        $this->assertEquals($numberOfRooms, $crawler->filter('li#admin-room-li')->count());
    }

    public function testAlertListForAdmin(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $admin = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findAdminUser();
        $client->loginUser($admin);

        // Accéder à la page principale du technicien
        $client->request('GET', '/admin-dashboard');


        // ************************* TEST **************************** \\

        //Vérifier que la liste des alertes est bien présente
        $this->assertSelectorExists('div#ALERT-List');
    }
}

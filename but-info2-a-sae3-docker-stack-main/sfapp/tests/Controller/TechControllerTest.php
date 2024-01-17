<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TechControllerTest extends WebTestCase
{
    //Tester l'acces au dashboard du technicien
    public function testAccessPage(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $technician = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findTechUser();
        $client->loginUser($technician);

        // Accéder à la page principale du technicien
        $client->request('GET', '/tech-dashboard');


        // ************************* TEST **************************** \\

        $this->assertResponseIsSuccessful();
    }

    public function testBlockTitle(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $technician = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findTechUser();
        $client->loginUser($technician);

        // Accéder à la page principale du technicien
        $client->request('GET', '/tech-dashboard');


        // ************************* TEST **************************** \\

        $this->assertSelectorTextContains('head title', 'Tech Dashboard');

        // Vérifier aussi que le titre visible sur la page est bien Tableau de bord
        $this->assertSelectorTextContains('span.dashboard-title', 'Tableau de bord - Technicien');
    }

    public function testTechNavbar(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $technician = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findTechUser();
        $client->loginUser($technician);

        // Accéder à la page principale du technicien
        $crawler = $client->request('GET', '/tech-dashboard');


        // ************************* TEST **************************** \\

        // Seule la navbar d'un utilisateur connecté contient l'élément "Logout"
        $this->assertSelectorExists('div.navbar a img[alt="Logout"]');
    }

    public function testAcquisitionSystemListForTech(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $technician = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findTechUser();
        $client->loginUser($technician);

        // Accéder à la page principale du technicien
        $crawler = $client->request('GET', '/tech-dashboard');


        // ************************* TEST **************************** \\

        //Seule la liste des AS du technicien contient le titre de "Systèmes d'acquisition installés"
        $this->assertSelectorTextContains('div.fs-5', "Systèmes d'acquisition installés");
    }

    public function testGlobalTechDashboard(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $technician = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findTechUser();
        $client->loginUser($technician);

        // Accéder à la page principale du technicien
        $crawler = $client->request('GET', '/tech-dashboard');


        // ************************* TEST **************************** \\

        // Vérifier que le technicien a bien accès à ses 3 colonnes de gestion
        // (Systèmes d'acquisition installés, Opérations à effectuer, Notifications)
        $this->assertEquals(3, $crawler->filter('div.col.panel.shadow')->count());

        //Vérifier que la première colonne qui à id="ASList" est bien celle des systèmes d'acquisition installés
        $this->assertSelectorExists('div#AS-Tech-List');
        $this->assertSelectorTextContains('div.fs-5', "Systèmes d'acquisition installés");

        //Vérifier que la deuxième colonne qui à id="OP-List" est bien celle des opérations à effectuer
        $this->assertSelectorExists('div#OP-List');
        $this->assertSelectorTextContains('div#OP-List', "Opérations à effectuer");

        //Vérifier que la troisième colonne qui à id="Notif-List" est bien celle des notifications
        $this->assertSelectorExists('div#Notif-List');
        $this->assertSelectorTextContains('div#Notif-List', "Notifications");
    }

    public function testAlertListForTech(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Connecter l'utilisateur tech
        $technician = $client->getContainer()->get('doctrine')->getRepository('App\Entity\User')->findTechUser();
        $client->loginUser($technician);

        // Accéder à la page principale du technicien
        $crawler = $client->request('GET', '/tech-dashboard');


        // ************************* TEST **************************** \\

        //Vérifier que la liste des alertes est bien présente
        $this->assertSelectorExists('div#ALERT-List');
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    public function testLoginPageAccess(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page de login
        $client->request('GET', '/login');


        // ************************* TEST **************************** \\

        $this->assertResponseIsSuccessful();
    }

    public function testLoginPageContent(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page de login
        $client->request('GET', '/login');


        // ************************* TEST **************************** \\

        $this->assertSelectorTextContains('head title', 'Login');
        $this->assertSelectorExists('form[action="/login"]');
        $this->assertSelectorExists('form input[name="_username"]');
        $this->assertSelectorExists('form input[name="_password"]');
        $this->assertSelectorExists('form button[type="submit"]');
    }

    public function testLoginFormSubmissionAdmin(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page de login
        $crawler = $client->request('GET', '/login');


        // ************************* TEST **************************** \\

        $form = $crawler->selectButton('SE CONNECTER')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $client->submit($form);

        $client->request('GET', '/admin-dashboard');

        $this->assertResponseIsSuccessful();


    }

    public function testLoginFormSubmissionTech(): void
    {
        // ************************ SETUP **************************** \\
        $client = static::createClient();

        // Accéder à la page de login
        $crawler = $client->request('GET', '/login');


        // ************************* TEST **************************** \\

        $form = $crawler->selectButton('SE CONNECTER')->form();

        $form['_username'] = 'tech';
        $form['_password'] = 'tech';

        $client->submit($form);

        $client->request('GET', '/tech-dashboard');

        $this->assertResponseIsSuccessful();
    }

}

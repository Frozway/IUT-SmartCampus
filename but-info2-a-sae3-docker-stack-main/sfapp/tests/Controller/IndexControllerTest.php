<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{
    private $client;
    private $crawler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->crawler = $this->client->request('GET', '/login');
    }

    public function testLoginPageAccess(): void
    {
        $this->assertResponseIsSuccessful();
    }

    public function testLoginPageContent(): void
    {
        $this->assertSelectorTextContains('head title', 'Login');
        $this->assertSelectorExists('form[action="/login"]');
        $this->assertSelectorExists('form input[name="_username"]');
        $this->assertSelectorExists('form input[name="_password"]');
        $this->assertSelectorExists('form button[type="submit"]');
    }

    public function testLoginFormSubmissionAdmin(): void
    {
        $form = $this->crawler->selectButton('SE CONNECTER')->form();

        $form['_username'] = 'admin';
        $form['_password'] = 'admin';

        $this->client->submit($form);

        $this->crawler = $this->client->request('GET', '/admin-dashboard');

        $this->assertResponseIsSuccessful();


    }

    public function testLoginFormSubmissionTech(): void
    {
        $form = $this->crawler->selectButton('SE CONNECTER')->form();

        $form['_username'] = 'tech';
        $form['_password'] = 'tech';

        $this->client->submit($form);

        $this->crawler = $this->client->request('GET', '/tech-dashboard');

        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Libérer les ressources si nécessaire
        $this->client = null;
        $this->crawler = null;
    }
}

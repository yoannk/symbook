<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testUserCanRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        /*$form = $crawler->selectButton('Créer un compte')->form([
            'registration[email]' => 'yoann.kergall@gmail.com',
            'registration[firstname]' => 'yoann',
            'registration[lastname]' => 'kergall',
            'registration[password][first]' => '1234',
            'registration[password][second]' => '12345',
        ]);

        $client->submit($form);*/

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}

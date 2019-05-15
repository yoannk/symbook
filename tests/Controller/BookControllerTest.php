<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(Book::NUM_ITEMS, $crawler->filter('tr.book'));
    }

    public function testUserCanCreateNewBook()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'yoann.kergall@gmail.com',
            'PHP_AUTH_PW' => '1234',
        ]);
        $client->followRedirects();

        $crawler = $client->request('GET', '/');
        $crawler = $client->clickLink('Ajouter un livre');
        $form = $crawler->selectButton('Ajouter')->form([
            'book[title]' => 'Mon super livre',
            'book[description]' => 'Ma super description',
            'book[image][file]' => __DIR__ . '/../../src/DataFixtures/images/0.jpg'
        ]);
        $crawler = $client->submit($form);

        $successFlashMessage = $crawler->filter('.alert-success')->text();

        $this->assertSame('Livre ajouté avec succès !', trim($successFlashMessage));

        $bookLink = $crawler->filter('table > tbody > tr')->first()->filter('td:nth-child(3) > a')->link();
        $crawler = $client->click($bookLink);

        $title = $crawler->filter('.card-title')->text();
        $author = $crawler->filter('.card-subtitle')->text();
        $description = $crawler->filter('.card-text')->text();

        $this->assertSame('Mon super livre', $title);
        $this->assertSame('Yoann Kergall', $author);
        $this->assertSame('Ma super description', $description);
    }

    public function testAnonymousCannotCreateNewBook()
    {
        $client = static::createClient();
        $client->request('GET', '/new');
        $response = $client->getResponse();

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->headers->get('Location'));
    }
}
<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $file = file_get_contents(__DIR__ . '/books.json');
        $books = json_decode($file);

        foreach ($books as $book) {
            $newBook = new Book();
            $newBook->setTitle($book->fields->titre);
            $newBook->setDescription($faker->paragraph());

            $manager->persist($newBook);
        }

        $manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    const FIXTURES_IMAGES_PATH = __DIR__.'/images';
    const UPLOADS_DIR = __DIR__.'/../../public/uploads';

    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $authors = $this->loadAuthors($manager, 10);
        $books = $this->loadBooks($manager, $authors, 50);

        $manager->flush();
    }

    /**
     * @return Book[]
     */
    private function loadBooks(ObjectManager $manager, $authors, $amount)
    {
        $books = [];

        for ($i = 0; $i < $amount; ++$i) {
            $book = new Book();
            $book->setTitle(implode(' ', $this->faker->words(rand(3, 7))));
            $book->setDescription($this->faker->paragraph());

            $imageId = rand(0, 99);
            // copy because the file will be moved
            copy(
                self::FIXTURES_IMAGES_PATH."/{$imageId}.jpg",
                self::FIXTURES_IMAGES_PATH."/{$imageId}-copy.jpg"
            );
            $file = new UploadedFile(
                self::FIXTURES_IMAGES_PATH."/{$imageId}-copy.jpg",
                $imageId.'.jpg',
                null,
                null,
                true // enable test mode
            );
            $fileName = md5(uniqid()).'.jpg';
            $file->move(self::UPLOADS_DIR, $fileName);

            $image = new Image();
            $image->setName($fileName);

            $book->setImage($image);
            $book->setAuthor($this->faker->randomElement($authors));

            $manager->persist($book);
            $books[] = $book;
        }

        return $books;
    }

    /**
     * @return Author[]
     */
    private function loadAuthors(ObjectManager $manager, $amount)
    {
        $authors = [];

        for ($i = 0; $i < $amount; ++$i) {
            $author = new Author();
            $author->setFirstname($this->faker->firstName);
            $author->setLastname($this->faker->lastName);
            $authors[] = $author;
        }

        return $authors;
    }
}

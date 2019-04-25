<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture
{
    const FIXTURES_IMAGES_PATH = __DIR__ . '/images';
    const UPLOADS_DIR = __DIR__ . '/../../public/uploads';

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $file = file_get_contents(__DIR__ . '/books.json');
        $books = json_decode($file);



        foreach ($books as $k => $book) {
            if ($k == 100) {
                break;
            }
            $newBook = new Book();
            $newBook->setTitle($book->fields->titre);
            $newBook->setDescription($faker->paragraph());

            $imageId = rand(0, 99);
            // copy because the file will be moved
            copy(
                self::FIXTURES_IMAGES_PATH . "/{$imageId}.jpg",
                self::FIXTURES_IMAGES_PATH . "/{$imageId}-copy.jpg"
            );
            $file = new UploadedFile(
                self::FIXTURES_IMAGES_PATH . "/{$imageId}-copy.jpg",
                $imageId . '.jpg',
                null,
                null,
                true // enable test mode
            );
            $fileName = md5(uniqid()) . '.jpg';
            $file->move(self::UPLOADS_DIR, $fileName);

            $image = new Image();
            $image->setName($fileName);

            $newBook->setImage($image);

            $manager->persist($newBook);
        }

        $manager->flush();
    }
}

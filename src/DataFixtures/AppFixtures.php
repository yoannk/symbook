<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const FIXTURES_IMAGES_PATH = __DIR__.'/images';
    const UPLOADS_DIR = __DIR__.'/../../public/uploads';

    private $faker;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $authors = $this->loadUsers($manager, 10);
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
     * @return User[]
     */
    private function loadUsers(ObjectManager $manager, $amount)
    {
        $authors = [];

        // User for functional tests
        $authors[] = $this->createUser(
            'Yoann',
            'Kergall',
            'gmail.com',
            '1234',
            'ROLE_USER'
        );

        // Random users
        for ($i = 0; $i < $amount; ++$i) {
            $author = $this->createUser(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->freeEmailDomain,
                '1234',
                'ROLE_USER'
            );

            $authors[] = $author;
        }

        return $authors;
    }

    private function createUser($firstname, $lastname, $emailDomain, $password, $role): User
    {
        $user = new User();
        $user->setEmail($this->toEmail($firstname, $lastname, $emailDomain));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRoles([$role]);
        $user->setEnabled(true);

        return $user;
    }

    private function toEmail($firstname, $lastname, $domain) {
        $string = $firstname . '.' . $lastname;
        $string = preg_replace('/\s+/', '.', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
        return $string . '@' . $domain;
    }
}

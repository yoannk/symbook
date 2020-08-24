<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates users and stores them in the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Email address: ', null, function ($value) {
            $violations = $this->validator->validate($value, [
                new NotBlank(),
                new Email()
            ]);

            if (count($violations) > 0) {
                throw new \Exception('Enter a valid email address');
            }

            return trim($value);
        });

        $firstname = $io->ask('Firstname: ', null, function ($value) {
            $violations = $this->validator->validate($value, [
                new NotBlank()
            ]);

            if (count($violations) > 0) {
                throw new \Exception('Enter a firstname');
            }

            return trim($value);
        });

        $lastname = $io->ask('Lastname: ', null, function ($value) {
            $violations = $this->validator->validate($value, [
                new NotBlank()
            ]);

            if (count($violations) > 0) {
                throw new \Exception('Enter a lastname');
            }

            return trim($value);
        });

        $password = $io->askHidden('Password (length >= 4) : ', function ($value) {
            $violations = $this->validator->validate($value, [
                new NotBlank(),
                new Length(['min' => 4])
            ]);

            if (count($violations) > 0) {
                throw new \Exception('Enter password with 4 or more characters');
            }

            return $value;
        });

        $io->askHidden('Repeat password : ', function ($value) use ($password) {
            $violations = $this->validator->validate($value, [
                new EqualTo($password)
            ]);

            if (count($violations) > 0) {
                throw new \Exception('Passwords must match');
            }

            return $value;
        });

        $isAdmin = $io->confirm('Is admin ? : ', false);

        $stopwatch = new Stopwatch();
        $stopwatch->start('create-user-command');

        $user = new User();
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $user->setRoles($isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);
        $user->setEnabled(true);

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success(sprintf(
            '%s was successfully created: %s %s (%s)',
            $isAdmin ? 'Administrator user' : 'User',
            $firstname,
            $lastname,
            $user->getEmail()
        ));

        $event = $stopwatch->stop('create-user-command');
        $io->comment(sprintf(
            'New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB',
            $user->getId(),
            $event->getDuration(),
            $event->getMemory() / (1024 ** 2)
        ));
    }

}

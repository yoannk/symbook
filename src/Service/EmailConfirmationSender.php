<?php

namespace App\Service;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use Twig\Environment;

class EmailConfirmationSender
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendEmail(User $user, EmailConfirmationToken $token)
    {
        $message = (new \Swift_Message('Veuillez confirmer votre email'))
            ->setFrom('noreply@symbook.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('email/confirmation.html.twig', ['token' => $token->getValue()]),
                'text/html'
            );

        $this->mailer->send($message);
    }
}
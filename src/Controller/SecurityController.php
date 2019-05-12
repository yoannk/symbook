<?php

namespace App\Controller;

use App\Entity\EmailConfirmationToken;
use App\Entity\ResetPasswordToken;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\LoginFormAuthenticator;
use App\Service\EmailConfirmationSender;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {

    }

    /**
     * @Route("/register", name="security_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ObjectManager $manager,
        EmailConfirmationSender $emailConfirmationSender
    )
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData())
            );

            $user->setRoles(['ROLE_USER']);

            $token = new EmailConfirmationToken($user);
            $emailConfirmationSender->sendEmail($user, $token);

            $manager->persist($user);
            $manager->persist($token);
            $manager->flush();

            $this->addFlash(
                'success',
                'Veuillez cliquer sur le lien dans l\'email de confirmation'
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm-email/{value}", name="security_confirm_email_token")
     */
    public function confirmEmailToken(
        $value,
        Request $request,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        ObjectManager $manager)
    {
        $token = $manager->getRepository(EmailConfirmationToken::class)->findOneBy(['value' => $value]);

        if (!$token instanceof EmailConfirmationToken) {
            return $this->redirectToRoute('book_index');
        }

        $user = $token->getUser();

        if ($user->isEnabled()) {
            $this->addFlash('info', 'Ce compte est déjà activé');
            return $this->redirectToRoute('book_index');
        }

        if ($token->isValid()) {
            $user->setEnabled(true);
            $manager->remove($token);
            $manager->flush();

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginFormAuthenticator,
                'main'
            );
        }

        $manager->remove($token);
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'info',
            'Le token est expiré, inscrivez-vous à nouveau'
        );

        return $this->redirectToRoute('security_register');
    }

    /**
     * @Route("/reset-password/{value}", name="security_reset_password_token")
     */
    public function resetPasswordToken(ResetPasswordToken $token)
    {

    }
}

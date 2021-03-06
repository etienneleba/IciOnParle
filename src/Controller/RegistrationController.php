<?php

namespace App\Controller;

use App\Entity\Registered;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use App\Service\EtherpadClient;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    /** @var EtherpadClient */
    private $etherpadClient;

    public function __construct(EmailVerifier $emailVerifier, EtherpadClient $etherpadClient)
    {
        $this->emailVerifier = $emailVerifier;
        $this->etherpadClient = $etherpadClient;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppAuthenticator $authenticator, ValidatorInterface $validator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setEtherpadAuthorId($this->etherpadClient->createAuthor($user->__toString()));

            $entityManager = $this->getDoctrine()->getManager();

            // check if the email is in the registered list
            $registered = $entityManager->getRepository(Registered::class)->findOneBy(['email' => $user->getEmail()]);
            if (null != $registered) {
                $entityManager->remove($registered);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@ici-on-parle.fr', 'IciOnParle'))
                    ->to($user->getEmail())
                    ->subject('IciOnParle : confirmation email')
                    ->htmlTemplate('_emails/confirmationEmail.html.twig')
            );
            $this->addFlash('success', 'Un email pour confirmation votre email vient de vous être envoyé');

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        $errors = $validator->validate($form);

        foreach ($errors as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        return $this->redirectToRoute('index', ['register' => true]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse mail a été vérifié.');

        return $this->redirectToRoute('app_register');
    }

    /**
     * @Route("/app/send/confirmation/email", name="app_send_confirmation_email")
     */
    public function sendEmailconfirmation()
    {
        $user = $this->getUser();
        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('contact@ici-on-parle.fr', 'IciOnParle'))
                ->to($user->getEmail())
                ->subject('IciOnParle : confirmation email')
                ->htmlTemplate('_emails/confirmationEmail.html.twig')
        );

        $this->addFlash('success', 'Un email pour confirmer votre email vient de vous être envoyé');

        return $this->redirectToRoute('app_dashboard');
    }
}

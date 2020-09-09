<?php

namespace App\Service;

use App\Entity\Registered;
use App\Entity\User;
use App\Form\RegisteredType;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FormGenerator
{
    private $formFactory;
    private $urlGenerator;
    private $tokenStorage;

    public function __construct(FormFactory $formFactory, UrlGeneratorInterface $urlGenerator, TokenStorageInterface $tokenStorage)
    {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
    }

    public function sayHello()
    {
        echo 'hello';
    }

    public function registrationForm()
    {
        $user = new User();

        $form = $this->formFactory->create(RegistrationFormType::class, $user, [
            'action' => $this->urlGenerator->generate('app_register'),
        ]);

        return $form->createView();
    }

    public function emailForm()
    {
        $registered = new Registered();

        $form = $this->formFactory->create(RegisteredType::class, $registered, [
            'action' => $this->urlGenerator->generate('register_email'),
        ]);

        return $form->createView();
    }
}

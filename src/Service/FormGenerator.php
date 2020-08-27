<?php

namespace App\Service;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
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

    public function profileForm()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $form = $this->formFactory->create(UserType::class, $user, [
            'action' => $this->urlGenerator->generate('app_user_edit'),
        ]);

        return $form->createView();
    }
}

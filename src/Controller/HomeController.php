<?php

namespace App\Controller;

use App\Entity\Registered;
use App\Form\RegisteredType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, EntityManagerInterface $em, AuthenticationUtils $authenticationUtils)
    {
        $registered = new Registered();

        $form = $this->createForm(RegisteredType::class, $registered);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($registered);

            $em->flush();

            $this->addFlash('success', 'Vous avez bien été ajouté à la liste');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('home/index.html.twig', [
            'registeredForm' => $form->createView(),
            'error' => $error,
            'lastUsername' => $lastUsername,
        ]);
    }
}

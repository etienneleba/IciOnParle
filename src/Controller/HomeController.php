<?php

namespace App\Controller;

use App\Entity\Registered;
use App\Form\RegisteredType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $registered = new Registered();

        $form = $this->createForm(RegisteredType::class, $registered);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($registered);

            $em->flush();

            $this->addFlash('success', 'Vous avez bien été ajouté à la liste');
        }

        return $this->render('home/index.html.twig', [
            'registeredForm' => $form->createView(),
        ]);
    }
}

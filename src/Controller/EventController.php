<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Registered;
use App\Form\RegisteredType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event", name="event_")
 */
class EventController extends AbstractController
{
    /** @var EntityManagerInterface */
    private $em;

    private $appAuthenticator;

    public function __construct(EntityManagerInterface $em, AppAuthenticator $appAuthenticator)
    {
        $this->em = $em;
        $this->appAuthenticator = $appAuthenticator;
    }

    /**
     * @Route("/viewAll", name="viewAll")
     */
    public function viewAll(Request $request, EntityManagerInterface $em)
    {
        $events = $this->em->getRepository(Event::class)->findAll();

        $registered = new Registered();

        $form = $this->createForm(RegisteredType::class, $registered);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($registered);

            $em->flush();

            $this->addFlash('success', 'Vous avez bien été ajouté à la liste');
        }

        return $this->render('event/viewAll.html.twig', [
            'registeredForm' => $form->createView(),
            'events' => $events,
        ]);
    }
}

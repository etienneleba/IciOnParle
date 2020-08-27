<?php

namespace App\Controller;

use App\Entity\Event;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function viewAll()
    {
        $events = $this->em->getRepository(Event::class)->findAll();

        return $this->render('event/viewAll.html.twig', [
            'events' => $events,
        ]);
    }
}

<?php

namespace App\Controller\App;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app", name="app_")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(EntityManagerInterface $em)
    {
        $userEvents = $em->getRepository(Event::class)->findAllWithUser($this->getUser());

        $events = $em->getRepository(Event::class)->findAllWithoutUser($this->getUser());

        return $this->render('app/main/dashboard.html.twig', [
            'userEvents' => $userEvents,
            'events' => $events,
        ]);
    }
}

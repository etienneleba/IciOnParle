<?php

namespace App\Controller\App;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\UserEvent;
use App\Service\EtherpadClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/app/event", name="app_event_")
 */
class EventController extends AbstractController
{
    private $etherpadClient;

    public function __construct(EtherpadClient $etherpadClient)
    {
        $this->etherpadClient = $etherpadClient;
    }

    /**
     * @Route("/view/{id}", name="view")
     */
    public function view(Event $event, EntityManagerInterface $em)
    {
        $group = $em->getRepository(Group::class)->findOneByUserEventStep($this->getUser(), $event, $event->getCurrentStep());

        return $this->render('app/event/view.html.twig', [
            'group' => $group,
            'event' => $event,
            'registered' => $event->isRegistered($this->getUser()),
        ]);
    }

    /**
     * @Route("/register/{id}", name="register")
     */
    public function register(Event $event, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $userEvent = (new UserEvent())
            ->setNbSources(0)
            ->setEvent($event)
            ->setUser($this->getUser())
        ;

        $event->addUserEvent($userEvent);

        $errors = $validator->validate($event);

        if (count($errors)) {
            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        } else {
            $em->flush();

            $this->addFlash('success', 'vous êtes inscrit à l\'événement : '.$event->getTitle());
        }

        return $this->redirectToRoute('app_event_view', ['id' => $event->getId()]);
    }

    /**
     * @Route("/unregister/{id}", name="unregister")
     */
    public function unregister(Event $event, EntityManagerInterface $em)
    {
        $userEvents = $em->getRepository(UserEvent::class)->findBy(['user' => $this->getUser(), 'event' => $event]);

        foreach ($userEvents as $userEvent) {
            $event->removeUserEvent($userEvent);
        }

        $em->flush();

        $this->addFlash('success', 'vous êtes désinscrit de l\'événement : '.$event->getTitle());

        return $this->redirectToRoute('app_event_view', ['id' => $event->getId()]);
    }
}

<?php

namespace App\Controller\App;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\Source;
use App\Entity\UserEvent;
use App\Form\SourceType;
use App\Service\EtherpadClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
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
    public function view(Event $event, Request $request, EntityManagerInterface $em)
    {
        if (false == $event->getStarted()) {
            return $this->redirectToRoute('app_dashboard');
        }
        /** @var Group $group */
        $group = $em->getRepository(Group::class)->findOneByUserStep($this->getUser(), $event->getCurrentStep());

        if (null == $group) {
            return $this->redirectToRoute('app_event_finished', ['id' => $event->getId()]);
        }

        $source = (new Source())
            ->setEvent($event)
        ;

        $sourceForm = $this->createForm(SourceType::class, $source);

        $sourceForm->handleRequest($request);

        if ($sourceForm->isSubmitted()) {
            if ($sourceForm->isValid()) {
                $event->addSource($source);

                $em->persist($source);

                $em->flush();
            }

            return $this->render('app/event/view.html.twig', [
                'group' => $group,
                'event' => $event,
                'sourceForm' => $sourceForm->createView(),
                'tab' => 'library',
            ]);
        }

        $validUntil = strtotime('+1 day');

        $sessionId = $this->etherpadClient->createSession($group->getEtherpadGroupId(), $this->getUser()->getEtherpadAuthorId(), $validUntil);

        $cookie = Cookie::create('sessionID')->withValue($sessionId)->withHttpOnly(false);

        $response = $this->render('app/event/view.html.twig', [
            'group' => $group,
            'event' => $event,
            'sourceForm' => $sourceForm->createView(),
        ]);

        $response->headers->setCookie($cookie);

        return $response;
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

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/unregister/{id}", name="unregister")
     */
    public function unregister(Event $event, EntityManagerInterface $em)
    {
        if ($event->getStarted()) {
            $this->addFlash('warning', 'L\'événement : '.$event->getTitle().' a déjà commencé, vous ne pouvez pas vous désinscrire');

            return $this->redirectToRoute('app_dashboard');
        }

        $userEvents = $em->getRepository(UserEvent::class)->findBy(['user' => $this->getUser(), 'event' => $event]);

        foreach ($userEvents as $userEvent) {
            $event->removeUserEvent($userEvent);
        }

        $em->flush();

        $this->addFlash('success', 'vous êtes désinscrit de l\'événement : '.$event->getTitle());

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * @Route("/finished/{id}", name="finished")
     */
    public function finished(Event $event)
    {
        return $this->render('app/event/finished.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/addSource/{id}", name="addSource")
     */
    public function addSource(Request $request, Event $event, EntityManagerInterface $em)
    {
        return $this->redirectToRoute('app_event_view', ['id' => $event->getId(), 'tab' => 'library']);
    }
}

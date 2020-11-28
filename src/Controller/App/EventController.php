<?php

namespace App\Controller\App;

use App\Entity\Event;
use App\Entity\Group;
use App\Entity\Source;
use App\Entity\User;
use App\Entity\UserEvent;
use App\Form\SourceType;
use App\Service\EtherpadClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

        if ($event->getFinished()) {
            return $this->redirectToRoute('app_event_finished', ['id' => $event->getId()]);
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
        /** @var User */
        $user = $this->getUser();
        if (!$user->isVerified()) {
            $confirmationPath = $this->generateUrl('app_send_confirmation_email');
            $this->addFlash('warning', 'Vous devez d\'abord confirmer votre email pour vous inscrire à un événement : <a href="'.$confirmationPath.'">Renvoyer un email</a>');

            return $this->redirectToRoute('app_dashboard');
        }
        $userEvent = (new UserEvent())
            ->setNbSources(0)
            ->setEvent($event)
            ->setUser($user)
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
     * @Route("/pdf/{id}", name="pdf")
     */
    public function pdf(Event $event)
    {
        if (!$event->getFinished()) {
            $this->addFlash('warning', 'L\'événement n\'est pas encore fini, vous ne pouvez pas télécharger le rapport final');

            return $this->redirectToRoute('app_event_view', ['id' => $event->getId()]);
        }
        if (null == $event->getPdfPath() || !file_exists($event->getPdfPath())) {
            $this->addFlash('warning', 'Le rapport final de cette événement n\'a pas été sauvegardé');

            return $this->redirectToRoute('app_event_view', ['id' => $event->getId()]);
        }

        $response = new BinaryFileResponse($event->getPdfPath());
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $event->getTitle().'.pdf'
        );

        return $response;
    }
}

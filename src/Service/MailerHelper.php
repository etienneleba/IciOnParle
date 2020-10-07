<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Registered;
use App\Entity\User;
use App\Entity\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;

class MailerHelper
{
    private $mailer;
    private $em;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function sendEmail(array $to, string $subject, string $template, array $context = [], array $attachements = []): bool
    {
        $email = (new TemplatedEmail())
            ->from('contact@ici-on-parle.fr')
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        foreach ($attachements as $attachement) {
            $email->attachFromPath($attachement);
        }

        foreach ($to as $address) {
            $email->addTo($address);
        }

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportException $e) {
        }

        return false;
    }

    public function sendEmailEventCreated(Event $event): void
    {
        $registereds = $this->em->getRepository(Registered::class)->findAll();

        /** @var Registered $registered */
        foreach ($registereds as $registered) {
            $this->sendEmail(
                [$registered->getEmail()],
                'IciOnParle, nouveau événement : '.$event->getTitle(),
                '_emails/eventCreated.html.twig',
                [
                    'event' => $event,
                ],
                []
            );
        }

        $users = $this->em->getRepository(User::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            $this->sendEmail(
                [$user->getEmail()],
                'IciOnParle, nouveau événement : '.$event->getTitle(),
                '_emails/eventCreated.html.twig',
                [
                    'user' => $user,
                    'event' => $event,
                ],
                []
            );
        }
    }

    public function sendEmailEventStarted(Event $event): void
    {
        /** @var UserEvent $userEvent */
        foreach ($event->getUserEvents() as $userEvent) {
            $this->sendEmail(
                [$userEvent->getUser()->getEmail()],
                "IciOnParle : l'événement {$event->getTitle()} a commencé !",
                '_emails/eventStarted.html.twig',
                [
                    'user' => $userEvent->getUser(),
                    'event' => $event,
                ],
                []
            );
        }
    }

    public function sendEmailEventNextStep(Event $event): void
    {
        $previousStepUsers = $event->getPreviousStep()->getUsers();
        $currentStepUsers = $event->getCurrentStep()->getUsers();
        $diffPreviousCurrentStepUsers = array_diff($previousStepUsers, $currentStepUsers);

        foreach ($currentStepUsers as $user) {
            $this->sendEmail(
                [$user->getEmail()],
                "IciOnParle, {$event->getTitle()} : vous avez été selectionné pour l'étape suivante !",
                '_emails/eventNextStepUsersSelected.html.twig',
                [
                    'user' => $user,
                    'event' => $event,
                ],
                []
            );
        }

        /** @var User $user */
        foreach ($diffPreviousCurrentStepUsers as $user) {
            $this->sendEmail(
                [$user->getEmail()],
                "IciOnParle, {$event->getTitle()} : un membre de votre groupe a été tiré au sort pour vous représenter",
                '_emails/eventNextStepUsersNotSelected.html.twig',
                [
                    'user' => $user,
                    'event' => $event,
                ],
                []
            );
        }
    }

    public function sendEmailEventFinished($event): void
    {
        foreach ($event->getUserEvents() as $userEvent) {
            $this->sendEmail(
                [$userEvent->getUser()->getEmail()],
                "IciOnParle : L'événement {$event->getTitle()} est fini, lisez le rapport",
                '_emails/eventFinished.html.twig',
                [
                    'user' => $userEvent->getUser(),
                    'event' => $event,
                ],
                []
            );
        }
    }
}

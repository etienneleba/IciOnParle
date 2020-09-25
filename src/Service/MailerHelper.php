<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Registered;
use App\Entity\User;
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

    public function sendMail(array $to, string $subject, string $template, array $context = [], array $attachements = []): bool
    {
        $email = (new TemplatedEmail())
            ->from('mail@ici-on-parle.fr')
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

    public function sendMailEventCreated(Event $event)
    {
        $registereds = $this->em->getRepository(Registered::class)->findAll();

        /** @var Registered $registered */
        foreach ($registereds as $registered) {
            $this->sendMail(
                [$registered->getEmail()],
                'IciOnParle : nouveau événement ! '.$event->getTitle(),
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
            $this->sendMail(
                [$user->getEmail()],
                'IciOnParle : nouveau événement ! '.$event->getTitle(),
                '_emails/eventCreated.html.twig',
                [
                    'user' => $user,
                    'event' => $event,
                ],
                []
            );
        }
    }
}

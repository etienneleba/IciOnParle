<?php

namespace App\EventSubscriber\Admin;

use App\Entity\Event;
use App\Service\MailerHelper;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    private $mailerHelper;

    public function __construct(MailerHelper $mailerHelper)
    {
        $this->mailerHelper = $mailerHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityPersistedEvent::class => ['sendMailEventCreated'],
        ];
    }

    public function sendMailEventCreated(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Event)) {
            return;
        }

        $this->mailerHelper->sendMailEventCreated($entity);
    }
}

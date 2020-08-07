<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\FieldProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class EventCrudController extends AbstractCrudController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function createEntity(string $entityFqcn)
    {
        return (new Event())
            ->setCreatedAt(new DateTime())
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX == $pageName) {
            return [
                IdField::new('id'),
                TextField::new('title', 'Titre'),
                TextEditorField::new('description', 'Description'),
                CollectionField::new('userEvents', 'Particpants'),
                NumberField::new('nbMaxUser', 'Maximum'),
                NumberField::new('step', 'Etape'),
            ];
        }

        return $this->get(FieldProvider::class)->getDefaultFields($pageName);
    }

    public function configureActions(Actions $actions): Actions
    {
        $startEvent = Action::new('startEvent', '', 'fa fa-play')
            ->linkToCrudAction('startEvent')->displayIf(function (Event $event) {
                return !$event->getStarted();
            })
        ;

        $nextStep = Action::new('nextStep', '', 'fa fa-step-forward')
            ->linkToCrudAction('nextStep')->displayIf(function (Event $event) {
                return $event->getStarted() && !$event->isFinalStep();
            })
        ;

        $endEvent = Action::new('endEvent', '', 'fa fa-stop')
            ->linkToCrudAction('endEvent')->displayIf(function (Event $event) {
                return $event->getStarted() && !$event->getFinished() && $event->isFinalStep();
            })
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $startEvent)
            ->add(Crud::PAGE_INDEX, $nextStep)
            ->add(Crud::PAGE_INDEX, $endEvent)
        ;
    }

    public function startEvent(AdminContext $adminContext)
    {
        /** @var Event $event */
        $event = $adminContext->getEntity()->getInstance();
        $event->addStep($event->nextStep($event->getCurrentStep()));

        $this->em->flush();

        return $this->redirect($this->get(CrudUrlGenerator::class)->build()->setController(EventCrudController::class)->setAction('index')->generateUrl());
    }

    public function nextStep(AdminContext $adminContext)
    {
        /** @var Event $event */
        $event = $adminContext->getEntity()->getInstance();
        $event->addStep($event->nextStep($event->getCurrentStep()));

        $this->em->flush();

        return $this->redirect($this->get(CrudUrlGenerator::class)->build()->setController(EventCrudController::class)->setAction('index')->generateUrl());
    }

    public function endEvent(AdminContext $adminContext)
    {
        /** @var Event $event */
        $event = $adminContext->getEntity()->getInstance();
        $event->setFinished(true);

        $this->em->flush();

        return $this->redirect($this->get(CrudUrlGenerator::class)->build()->setController(EventCrudController::class)->setAction('index')->generateUrl());
    }
}

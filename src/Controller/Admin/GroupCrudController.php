<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Group::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('etherpadGroupId', 'Etherpad group id'),
            TextField::new('etherpadPadId', 'Etherpad pad id'),
            CollectionField::new('users', 'Participants')->hideOnIndex(),
            NumberField::new('nbUsers', 'Participants')->onlyOnIndex(),
            TextareaField::new('finalText', 'Final text'),
            AssociationField::new('step', 'Step'),
        ];
    }
}

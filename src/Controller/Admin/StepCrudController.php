<?php

namespace App\Controller\Admin;

use App\Entity\Step;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class StepCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Step::class;
    }

    public function configureFields(string $pageName): iterable
    {
        if (Crud::PAGE_INDEX == $pageName) {
            return [
                IdField::new('id'),
                NumberField::new('rank', 'Rank'),
                CollectionField::new('groups', 'Nombre de groupes'),
                NumberField::new('nbParticipants', 'Partcipants'),
                BooleanField::new('finalStep', 'Etape finale'),
            ];
        }

        return $this->get(FieldProvider::class)->getDefaultFields($pageName);
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}

<?php

namespace App\Controller\Admin;

use App\Entity\Registered;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class RegisteredCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Registered::class;
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

<?php

namespace App\Controller\Admin;

use App\Entity\SocialNetworkType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SocialNetworkTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SocialNetworkType::class;
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

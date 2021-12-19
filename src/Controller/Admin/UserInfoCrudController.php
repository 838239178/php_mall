<?php

namespace App\Controller\Admin;

use App\Entity\UserInfo;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserInfoCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserInfo::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX,Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('userId')->hideOnForm(),
            TextField::new('username')->hideOnForm(),
            TextField::new('nickName'),
            TextField::new("salt")->onlyOnDetail(),
            TextField::new('email')->hideOnForm(),
            CollectionField::new('roles')
                ->setFormTypeOptions([
                    'allow_add' => true,
                    'allow_delete' => true,
                ])
        ];
    }
}

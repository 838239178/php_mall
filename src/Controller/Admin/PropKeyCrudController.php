<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\PropKey;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PropKeyCrudController extends AbstractCrudController
{
    private CategoryRepository $categoryRepo;

    /**
     * PropKeyCrudController constructor.
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }


    public static function getEntityFqcn(): string
    {
        return PropKey::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('keyId')->onlyOnIndex(),
            IdField::new('createUid')->onlyOnIndex(),
            TextField::new('keyName',"属性名"),
            AssociationField::new('category','分类')
                ->setFormTypeOption('choice_label', 'categoryName')
                ->setFormTypeOption('class', Category::class)
        ];
    }
}

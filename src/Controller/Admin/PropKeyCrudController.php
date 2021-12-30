<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\PropKey;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

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

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new("category", "分类"));
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public static function getEntityFqcn(): string
    {
        return PropKey::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('keyId')->onlyOnIndex(),
            IdField::new('createUid')->onlyOnDetail(),
            TextField::new('keyName',"属性名"),
            AssociationField::new('category','分类')
                ->setFormTypeOption('choice_label', 'categoryName')
                ->setFormTypeOption('class', Category::class)
        ];
    }
}

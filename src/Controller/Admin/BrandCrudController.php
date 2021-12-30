<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use SebastianBergmann\CodeCoverage\Report\Text;

class BrandCrudController extends AbstractCrudController
{
    private CategoryRepository $categoryRepo;

    /**
     * BrandCrudController constructor.
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }


    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new("category", "分类"));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('brandId')->onlyOnIndex(),
            TextField::new('brandName','品牌名称'),
            TextField::new('brandDesc','品牌描述'),
            ImageField::new('logo','品牌图标')
                ->setBasePath('uploads/images')
                ->setUploadDir('public/uploads/images'),
            AssociationField::new('category','分类')
                ->setFormTypeOption('choice_label', 'categoryName')
                ->setFormTypeOption('class', Category::class),
        ];
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductPropKeyType;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    private BrandRepository $brandRepo;
    private CategoryRepository $categoryRepo;

    /**
     * ProductCrudController constructor.
     * @param BrandRepository $brandRepo
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(BrandRepository $brandRepo, CategoryRepository $categoryRepo)
    {
        $this->brandRepo = $brandRepo;
        $this->categoryRepo = $categoryRepo;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('productId')->hideOnForm(),
            IdField::new('shopId')->hideOnForm()->hideOnDetail()->hideOnIndex(),
            TextField::new('productName', '商品名称'),
            TextField::new('productDesc', '商品描述'),
            ImageField::new('previewImg', '商品图片')
                ->setBasePath('uploads/images')
                ->setUploadDir('public/uploads/images'),
            ChoiceField::new('productStatus', '发布状态')
                ->setChoices(fn() => ['不发布' => 'undeployed', '发布' => 'deployed']),
            TextField::new("productTags","商品标签")->onlyOnForms(),
            AssociationField::new("brand", "品牌")
                ->setFormTypeOption('choice_label', 'brandName')
                ->setFormTypeOption('class', Brand::class),
            AssociationField::new("category", "分类")
                ->setFormTypeOption('choice_label', 'categoryName')
                ->setFormTypeOption('class', Category::class),
            CollectionField::new('propKeys')
                ->hideOnIndex()
                ->setFormTypeOption('by_reference',false)
                ->setFormTypeOption('entry_type', ProductPropKeyType::class),
            DateTimeField::new('createTime')->onlyOnIndex(),
            DateTimeField::new("deployTime")->onlyOnIndex(),
        ];
    }
}

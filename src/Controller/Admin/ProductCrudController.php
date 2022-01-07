<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Orders;
use App\Entity\Product;
use App\Form\ProductPropKeyType;
use App\Form\RichTextEditField;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Flex\Response;

class ProductCrudController extends AbstractCrudController
{
    private BrandRepository $brandRepo;
    private CategoryRepository $categoryRepo;
    private string $frontend;
    private JWTTokenManagerInterface $JWTTokenManager;

    /**
     * ProductCrudController constructor.
     * @param BrandRepository $brandRepo
     * @param CategoryRepository $categoryRepo
     */
    public function __construct(BrandRepository $brandRepo, CategoryRepository $categoryRepo, string $frontend, JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->brandRepo = $brandRepo;
        $this->categoryRepo = $categoryRepo;
        $this->frontend = $frontend;
        $this->JWTTokenManager = $JWTTokenManager;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->addFormTheme("@KMSFroalaEditor/Form/froala_widget.html.twig");
    }

    public function showChart(AdminContext $adminContext, AdminUrlGenerator $urlGenerator): RedirectResponse
    {
        /** @var Product $product */
        $product = $adminContext->getEntity()->getInstance();
        $tempToken = $this->JWTTokenManager->createFromPayload($adminContext->getUser(), ['exp' => time() + 300]);
        return $this->redirect($this->frontend."/charts?productId=".$product->getProductId()."&token=$tempToken");
    }

    public function configureActions(Actions $actions): Actions
    {
        $showChart = Action::new('showChart', '报表')
            ->linkToCrudAction('showChart')
            ->addCssClass('btn-sm');
        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $showChart)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
                ->add(DateTimeFilter::new("createTime", "创建时间"))
                ->add(EntityFilter::new("shop", "商店"))
                ->add(EntityFilter::new("category", "分类"))
                ->add(ChoiceFilter::new("productStatus", "状态")->setChoices([
                    "未发布"=>"undeployed",
                    "已发布"=>"deployed",
                    "已失效"=>"invalid"
                ]));
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
            NumberField::new("lowestPrice", "最低价")
                ->hideOnForm()
                ->formatValue(fn($v)=> $v." 元")
                ->setFormTypeOption("scale", 2),
            CollectionField::new('propKeys')
                ->hideOnIndex()
                ->setFormTypeOption('by_reference',false)
                ->setFormTypeOption('entry_type', ProductPropKeyType::class),
            DateTimeField::new('createTime')->onlyOnIndex(),
            DateTimeField::new("deployTime")->onlyOnDetail(),
            RichTextEditField::new("introPage", "介绍")
                ->hideOnIndex()
        ];
    }
}

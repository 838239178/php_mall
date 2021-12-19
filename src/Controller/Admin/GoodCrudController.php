<?php

namespace App\Controller\Admin;

use App\Entity\Good;
use App\Entity\Product;
use App\Form\GoodPropKeyType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;

class GoodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Good::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        $prod = AssociationField::new('product', '对应商品')
            ->setFormTypeOptions([
                'class' => Product::class,
                'choice_label' => "productName"
            ]);
        return [
            IdField::new('goodId')->hideOnForm(),
            $prod,
            NumberField::new('originalPrice', '原价')
                ->formatValue(fn($v)=> $v." 元")
                ->setFormTypeOption("scale", 2),
            NumberField::new('salePrice', '售价')
                ->formatValue(fn($v)=> $v." 元")
                ->setFormTypeOption("scale", 2),
            IntegerField::new('stock', '库存数量'),
            CollectionField::new('propKeys', "货品属性")
                ->onlyWhenUpdating()
                ->setFormTypeOptions([
                    'entry_type' => GoodPropKeyType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'product_provider' => fn() => $prod->getAsDto()->getValue()
                    ]
                ])
        ];
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("shopId")->onlyOnIndex(),
            TextField::new("shopName"),
            ImageField::new("shopIcon")
                ->setBasePath('uploads/images')
                ->setUploadDir('public/uploads/images'),
            TextField::new("expressName", label: "默认快递名称"),
            NumberField::new("expressPrice", label: "默认快递价格")
                ->formatValue(fn($v)=> $v." 元")
                ->setFormTypeOption("scale", 2),
            TextField::new("expressAddr", label: "默认发货地址")
        ];
    }
}

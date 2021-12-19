<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Psr\Log\LoggerInterface;

class CategoryCrudController extends AbstractCrudController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $log)
    {
        $this->logger = $log;
    }


    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('categoryId')->onlyOnIndex(),
            TextField::new('categoryName', '分类名称'),
            TextField::new('categoryDesc', '分类描述'),
            IntegerField::new("categoryLevel", "分类级别")->hideOnForm(),
            AssociationField::new('parent', '上级分类')
                ->setFormTypeOption("choice_label", "categoryName")
                ->setFormTypeOption("class", Category::class)
                ->setQueryBuilder(fn(QueryBuilder $er) =>
                    $er->where("entity.categoryLevel < 3")
                ),
            DateTimeField::new('createTime', '创建时间')->onlyOnIndex(),
        ];
    }
}

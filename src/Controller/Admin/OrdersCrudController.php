<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Form\OrdersDetailType;
use App\Workflow\OrderWorkflow;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class OrdersCrudController extends AbstractCrudController
{
    private OrderWorkflow $workflow;
    private array $stateArray = [
        "待付款" => "wait_pay",
        "待退款" => "wait_draw_back",
        "待发货" => "wait_express",
        "待收货" => "wait_receive",
        "已完成" => "finished",
        "已取消" => "canceled"
    ];

    /**
     * OrdersCrudController constructor.
     * @param OrderWorkflow $workflow
     */
    public function __construct(OrderWorkflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public static function getEntityFqcn(): string
    {
        return Orders::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)->showEntityActionsInlined();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new("shop", "商店"))
            ->add(ChoiceFilter::new("ordersStatus", "状态")->setChoices($this->stateArray))
            ->add(DateTimeFilter::new('createTime', "下单时间"));
    }

    public function express(AdminContext $adminContext, AdminUrlGenerator $urlGenerator): Response
    {
        $order = $adminContext->getEntity()->getInstance();
        $this->workflow->express($order);
        return $this->redirect($urlGenerator->setAction(Action::INDEX)->generateUrl());
    }

    public function drawback(AdminContext $adminContext, AdminUrlGenerator $urlGenerator):Response
    {
        $order = $adminContext->getEntity()->getInstance();
        $this->workflow->drawback($order);

        return $this->redirect($urlGenerator->setAction(Action::INDEX)->generateUrl());
    }
    public function noDrawBack(AdminContext $adminContext, AdminUrlGenerator $urlGenerator): Response
    {
        $order = $adminContext->getEntity()->getInstance();
        $this->workflow->cancelDrawBack($order);
        return $this->redirect($urlGenerator->setAction(Action::INDEX)->generateUrl());
    }

    public function configureActions(Actions $actions): Actions
    {
        $expressAction = Action::new('express', '发货', 'fa fa-check')
            ->linkToCrudAction('express')
            ->displayIf(fn(Orders $orders) => $this->workflow->canExpress($orders))
            ->addCssClass('btn-sm btn-success');

        $drawbackAction = Action::new('drawback', '退款', 'fa fa-check')
            ->linkToCrudAction('drawback')
            ->displayIf(fn(Orders $orders) => $this->workflow->canDrawBack($orders))
            ->addCssClass('btn-sm btn-warning');

        $noDrawbackAction = Action::new('noDrawBack', '不同意', 'faa fa-times')
            ->linkToCrudAction('noDrawBack')
            ->displayIf(fn(Orders $orders) => $this->workflow->canDrawBack($orders))
            ->addCssClass('btn-sm btn-warning');

        return parent::configureActions($actions)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $expressAction)
            ->add(Crud::PAGE_INDEX, $drawbackAction)
            ->add(Crud::PAGE_INDEX, $noDrawbackAction);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("ordersId")
                ->onlyOnIndex(),
            TextField::new("ordersStatus","订单状态")
                ->onlyOnIndex()
                ->formatValue(function ($value) {
                    foreach ($this->stateArray as $key=>$val) {
                        if ($val == $value) {
                            return $key;
                        }
                    }
                    return $value;
                }),
            TextField::new("expressId", "快递单号"),
            TextField::new("address", "收货地址"),
            NumberField::new("expressPrice", "快递价格")
                ->formatValue(fn($v) => $v . " 元")
                ->setFormTypeOption("scale", 2),
            NumberField::new("totalPrice", "总价")->hideOnForm()
                ->formatValue(fn($v) => $v . " 元")
                ->setFormTypeOption("scale", 2),
            TextField::new("expressName", "快递名称")
                ->hideOnIndex(),
            TextField::new("expressAddress", "发货地址")
                ->hideOnIndex(),
            DateTimeField::new("createTime", "下单日期")->onlyOnDetail(),
            DateTimeField::new("refundTime", "退款日期")->onlyOnDetail(),
            DateTimeField::new("finishTime", "完成日期")->onlyOnDetail(),
            DateTimeField::new("expressTime", "发货日期")->onlyOnDetail(),
            DateTimeField::new("payTime", "支付日期")->onlyOnDetail(),
            CollectionField::new("details", "详单")
                ->onlyOnDetail()
        ];
    }

}

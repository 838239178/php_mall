<?php

namespace App\Controller\Admin;

use App\Entity\Orders;
use App\Workflow\OrderWorkflow;
use Doctrine\ORM\EntityManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class OrdersCrudController extends AbstractCrudController
{
    private OrderWorkflow $workflow;

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

    public function express(AdminContext $adminContext, AdminUrlGenerator $urlGenerator): Response {
        $order = $adminContext->getEntity()->getInstance();
        $this->workflow->express($order);

        return $this->redirect($urlGenerator->generateUrl());
    }

    public function drawback(AdminContext $adminContext, AdminUrlGenerator $urlGenerator): Response {
        $order = $adminContext->getEntity()->getInstance();
        $this->workflow->cancel($order);

        return $this->redirect($urlGenerator->generateUrl());
    }

    public function configureActions(Actions $actions): Actions
    {
        $expressAction = Action::new('express', '发货', 'fa fa-check')
            ->linkToCrudAction('express')
            ->displayIf(fn (Orders $orders) => $this->workflow->canExpress($orders))
            ->addCssClass('btn-sm btn-success');

        $drawbackAction = Action::new('drawback', '退款', 'fa fa-check')
            ->linkToCrudAction('drawback')
            ->displayIf(fn (Orders $orders) => $this->workflow->canCancel($orders))
            ->addCssClass('btn-sm btn-warning');

        return parent::configureActions($actions)
            ->remove(Crud::PAGE_INDEX,Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $expressAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $drawbackAction);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new("ordersId")->onlyOnIndex(),
            TextField::new("ordersStatus")->onlyOnIndex(),
            TextField::new("expressId"),
            TextField::new("address"),
            NumberField::new("expressPrice")
                ->formatValue(fn($v)=> $v." 元")
                ->setFormTypeOption("scale", 2),
            NumberField::new("totalPrice")->hideOnForm()
                ->formatValue(fn($v)=> $v." 元")
                ->setFormTypeOption("scale", 2),
            TextField::new("expressName")->hideOnIndex(),
            TextField::new("expressAddress")->hideOnIndex(),
            DateTimeField::new("createTime")->onlyOnDetail(),
            DateTimeField::new("refundTime")->onlyOnDetail(),
            DateTimeField::new("finishTime")->onlyOnDetail(),
            DateTimeField::new("expressTime")->onlyOnDetail(),
            DateTimeField::new("payTime")->onlyOnDetail(),
        ];
    }

}

<?php


namespace App\Workflow;


use App\Entity\Orders;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Workflow\Exception\TransitionException;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderWorkflow
{
    const CANCEL = "cancel";
    const REQ_DRAWBACK = "req_drawback";
    const EXPRESS = "express";
    const PAY = "pay";
    const FINISH = "finish";
    const DRAWBACK = "drawback";


    private WorkflowInterface $workflow;
    private EntityManagerInterface $em;

    public function __construct(WorkflowInterface $ordersFinishingStateMachine, EntityManagerInterface $entityManager)
    {
        $this->workflow = $ordersFinishingStateMachine;
        $this->em=$entityManager;
    }

    public function can(Orders $orders, string $trans):bool {
        return $this->workflow->can($orders, $trans);
    }

    public function canExpress(Orders $orders): bool {
        return $this->workflow->can($orders, self::EXPRESS);
    }

    public function canCancel(Orders $orders):bool {
        return $this->workflow->can($orders, self::CANCEL);
    }

    public function canPay(Orders $orders):bool{
        return $this->workflow->can($orders, self::PAY);
    }

    public function canFinish(Orders $orders): bool {
        return $this->workflow->can($orders, self::FINISH);
    }

    public function canDrawBack(Orders $orders): bool {
        return $this->workflow->can($orders, self::DRAWBACK);
    }

    public function express(Orders $orders) {
        $orders->setOrdersStatus('wait_receive');
        $this->em->flush();
    }

    public function cancel(Orders $orders) {
        $this->workflow->apply($orders,self::CANCEL);
        $this->em->flush();
    }

    public function cancelDrawBack(Orders $orders) {
        if($orders->getExpressTime() != null) {
            $orders->setOrdersStatus('wait_receive');
        } else {
            $orders->setOrdersStatus('wait_express');
        }
        $this->em->flush();
    }

    public function apply(Orders $orders, string $trans)
    {
        $this->workflow->apply($orders, $trans);
        $this->em->flush();
    }
}
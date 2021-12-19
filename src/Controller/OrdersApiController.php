<?php


namespace App\Controller;

use App\Consts\Role;
use App\Entity\UserInfo;
use App\Repository\OrdersRepository;
use App\Util\HttpUtils;
use App\Workflow\OrderWorkflow;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/api/orders', name: 'api_orders')]
#[IsGranted(Role::USER)]
class OrdersApiController extends AbstractController
{
    private OrderWorkflow $workflow;
    private OrdersRepository $ordersRepository;

    public function __construct(OrderWorkflow $workflow, OrdersRepository $ordersRepository)
    {
        $this->workflow = $workflow;
        $this->ordersRepository = $ordersRepository;
    }

    #[Route(path: '/{id}/cancel', name: '_cancel', methods: ['PATCH'])]
    public function cancel(int $id, #[CurrentUser] UserInfo $userInfo): Response
    {
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->canCancel($orders)
        ) {
            $this->workflow->cancel($orders);
        } else {
            return HttpUtils::wrapperFail("不可取消这个订单");
        }
        return HttpUtils::wrapperSuccess(message: '取消成功');
    }

    #[Route(path: '/{id}/drawback', name: '_drawback', methods: ['PATCH'])]
    public function requestDrawback(int $id, #[CurrentUser] UserInfo $userInfo): Response
    {
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->can($orders, OrderWorkflow::REQ_DRAWBACK)
        ) {
            $this->workflow->apply($orders, OrderWorkflow::REQ_DRAWBACK);
        } else {
            return HttpUtils::wrapperFail("这个订单发起不可退款");
        }
        return HttpUtils::wrapperSuccess(message: '发起退款成功');
    }

    #[Route(path: '/{id}/pay', name: '_pay', methods: ['PATCH'])]
    public function pay(int $id, #[CurrentUser] UserInfo $userInfo): Response
    {
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->canPay($orders)
        ) {
            $this->workflow->apply($orders, OrderWorkflow::PAY);
        } else {
            return HttpUtils::wrapperFail("这个订单发起不可支付");
        }
        return HttpUtils::wrapperSuccess(message: '支付成功');
    }
}
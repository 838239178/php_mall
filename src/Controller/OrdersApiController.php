<?php


namespace App\Controller;

use App\Consts\Role;
use App\Entity\UserInfo;
use App\Repository\OrdersRepository;
use App\Util\HttpUtils;
use App\Workflow\OrderWorkflow;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/api/orders', name: 'api_orders')]
#[IsGranted(Role::USER)]
class OrdersApiController extends AbstractController
{
    private OrderWorkflow $workflow;
    private OrdersRepository $ordersRepository;
    private HttpUtils $httpUtils;
    private Security $security;

    /**
     * OrdersApiController constructor.
     * @param OrderWorkflow $workflow
     * @param OrdersRepository $ordersRepository
     * @param HttpUtils $httpUtils
     * @param Security $security
     */
    public function __construct(OrderWorkflow $workflow, OrdersRepository $ordersRepository, HttpUtils $httpUtils, Security $security)
    {
        $this->workflow = $workflow;
        $this->ordersRepository = $ordersRepository;
        $this->httpUtils = $httpUtils;
        $this->security = $security;
    }

    #[Route(path: '/{id}/cancel', name: '_cancel', methods: ['PATCH'])]
    public function cancel(int $id): Response
    {
        /** @var UserInfo $userInfo */
        $userInfo = $this->security->getUser();
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->canCancel($orders)
        ) {
            $this->workflow->cancel($orders);
        } else {
            return $this->httpUtils->wrapperFail("不可取消这个订单");
        }
        return $this->httpUtils->wrapperSuccess();
    }

    #[Route(path: '/{id}/cancel_drawback', name: '_cancel_drawback', methods: ['PATCH'])]
    public function cancelDrawBack(int $id): Response
    {
        /** @var UserInfo $userInfo */
        $userInfo = $this->security->getUser();
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->canDrawBack($orders)
        ) {
            $this->workflow->cancelDrawBack($orders);
        } else {
            return $this->httpUtils->wrapperFail("这个订单不可退款");
        }
        return $this->httpUtils->wrapperSuccess();
    }

    #[Route(path: '/{id}/drawback', name: '_drawback', methods: ['PATCH'])]
    public function requestDrawback(int $id): Response
    {
        /** @var UserInfo $userInfo */
        $userInfo = $this->security->getUser();
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->can($orders, OrderWorkflow::REQ_DRAWBACK)
        ) {
            $this->workflow->apply($orders, OrderWorkflow::REQ_DRAWBACK);
        } else {
            return $this->httpUtils->wrapperFail("这个订单不可退款");
        }
        return $this->httpUtils->wrapperSuccess();
    }

    #[Route(path: '/{id}/receive', name: '_receive', methods: ['PATCH'])]
    public function receive(int $id): Response
    {
        /** @var UserInfo $userInfo */
        $userInfo = $this->security->getUser();
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->can($orders, OrderWorkflow::FINISH)
        ) {
            $this->workflow->apply($orders, OrderWorkflow::FINISH);
        } else {
            return $this->httpUtils->wrapperFail("forbidden");
        }
        return $this->httpUtils->wrapperSuccess();
    }

    #[Route(path: '/{id}/remove', name: '_receive', methods: ['PATCH','DELETE'])]
    public function remove(int $id, EntityManagerInterface $em): Response
    {
        /** @var UserInfo $userInfo */
        $userInfo = $this->security->getUser();
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $orders->getOrdersStatus() == 'canceled'
        ) {
            $em->remove($orders);
            $em->flush();
        } else {
            return $this->httpUtils->wrapperFail("forbidden", Response::HTTP_FORBIDDEN);
        }
        return $this->httpUtils->wrapperSuccess(responseCode: Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/{id}/pay', name: '_pay', methods: ['PATCH'])]
    public function pay(int $id): Response
    {
        /** @var UserInfo $userInfo */
        $userInfo = $this->security->getUser();
        $orders = $this->ordersRepository->find($id);
        if (
            $orders != null &&
            $orders->getUser()->getUserId() === $userInfo->getUserId() &&
            $this->workflow->canPay($orders)
        ) {
            $orders->setPayId(999999999999999);
            $this->workflow->apply($orders, OrderWorkflow::PAY);
        } else {
            return $this->httpUtils->wrapperFail("这个订单不可支付");
        }
        return $this->httpUtils->wrapperSuccess();
    }
}
<?php


namespace App\Service;


use App\Repository\OrdersRepository;

class OrdersService
{
    private OrdersRepository $ordersRepository;

    /**
     * OrdersService constructor.
     * @param OrdersRepository $ordersRepository
     */
    public function __construct(OrdersRepository $ordersRepository)
    {
        $this->ordersRepository = $ordersRepository;
    }



}
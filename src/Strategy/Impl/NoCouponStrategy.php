<?php


namespace App\Strategy\Impl;


use App\Entity\Orders;
use App\Entity\OrdersDetail;
use App\Strategy\OrderCalcStrategy;

class NoCouponStrategy implements OrderCalcStrategy
{
    public function calc(Orders $orders, array $options=[]): float
    {
        $total = 0;
        /** @var OrdersDetail $detail */
        foreach ($orders->getDetails() as $detail) {
            $good = $detail->getGood();
            $total += $good->getSalePrice() * $detail->getProductSize();
        }
        return $total + $orders->getExpressPrice();
    }

}
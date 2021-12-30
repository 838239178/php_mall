<?php


namespace App\Strategy;


use App\Entity\Orders;

interface OrderCalcStrategy
{
    public function calc(Orders $orders, array $options=[]): float;
}
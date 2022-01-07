<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ShopMonthChart
{
    /**
     * @ORM\Id
     * @ORM\Column(name="month", type="integer")
     */
    private int $month;

    /**
     * @var float
     *
     * @ORM\Column(name="money", type="decimal", scale=2, precision=10)
     */
    private float $money;

    /**
     * @return float
     */
    public function getMoney(): float
    {
        return $this->money;
    }

    /**
     * @param float $money
     */
    public function setMoney(float $money): void
    {
        $this->money = $money;
    }
}
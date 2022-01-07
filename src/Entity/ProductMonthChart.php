<?php


namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ProductMonthChart
{
    /**
     * @ORM\Id
     * @ORM\Column(name="month", type="integer")
     */
    private int $month;


    /**
     * @var int
     *
     * @ORM\Column(name="sale", type="integer")
     */
    private int $sale;

    /**
     * @var float
     *
     * @ORM\Column(name="money", type="decimal", scale=2, precision=10)
     */
    private float $money;

    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * @param int $month
     */
    public function setMonth(int $month): void
    {
        $this->month = $month;
    }

    /**
     * @return int
     */
    public function getSale(): int
    {
        return $this->sale;
    }

    /**
     * @param int $sale
     */
    public function setSale(int $sale): void
    {
        $this->sale = $sale;
    }

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
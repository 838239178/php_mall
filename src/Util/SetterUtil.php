<?php


namespace App\Util;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use KaiGrassnick\SnowflakeBundle\Generator\SnowflakeGenerator;

class SetterUtil
{
    private static ?SnowflakeGenerator $snowFlake = null;

    public static function getSnowFlake(): SnowflakeGenerator
    {
        if (self::$snowFlake == null) {
            self::$snowFlake = new SnowflakeGenerator();
        }
        return self::$snowFlake;
    }

    public static function setSnowFlakeId(int& $src) {
        $src = self::getSnowFlake()->generateSnowflake();
    }

    public static function setCollection(Collection& $src, Collection|array $value)
    {
        if ($value instanceof ArrayCollection){
            $src = $value;
        } else if (is_array($value)) {
            $src = new ArrayCollection($value);
        }
    }
}
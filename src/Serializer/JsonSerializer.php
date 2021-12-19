<?php


namespace App\Serializer;


use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class JsonSerializer
{
    private static Serializer|null $instance = null;

    public static function getInstance(): Serializer
    {
        if(self::$instance == null) {
            self::$instance = SerializerBuilder::create()->build();
        }
        return self::$instance;
    }
}
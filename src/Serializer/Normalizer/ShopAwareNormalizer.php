<?php


namespace App\Serializer\Normalizer;


use App\Entity\Shop;
use App\Serializer\Normalizer\Base\AbstractAwareNormalizer;

class ShopAwareNormalizer extends AbstractAwareNormalizer
{
    function getSupportedClass(): string
    {
        return Shop::class;
    }

    protected function afterNormalize(array &$data)
    {
        if(isset($data["shopIcon"])) {
            $data["icon"] = $this->getImageUrlPrefix().$data["shopIcon"];
        }
    }
}
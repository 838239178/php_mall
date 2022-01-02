<?php

namespace App\Serializer\Normalizer;

use App\Entity\ProductPropKey;
use App\Serializer\Normalizer\Base\AbstractAwareNormalizer;

class ProductPropKeyNormalizer extends AbstractAwareNormalizer
{
    function getSupportedClass(): string
    {
        return ProductPropKey::class;
    }

    protected function afterNormalize(array &$data)
    {
        //数组化可选值
        $data['optionValues'] = preg_split("~,~", $data['optionValues']);
    }
}

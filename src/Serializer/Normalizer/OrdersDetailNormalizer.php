<?php


namespace App\Serializer\Normalizer;


use App\Entity\OrdersDetail;
use App\Serializer\Normalizer\Base\AbstractAwareNormalizer;

class OrdersDetailNormalizer extends AbstractAwareNormalizer
{
    function getSupportedClass(): string
    {
        return OrdersDetail::class;
    }

    protected function afterNormalize(array &$data)
    {
        if (isset($data['productImg'])) {
            $data['productImgUrl'] = $this->getImageUrlPrefix().$data['productImg'];
        }
    }

}
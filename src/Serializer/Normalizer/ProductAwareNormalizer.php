<?php


namespace App\Serializer\Normalizer;

use App\Entity\Product;
use App\Serializer\Normalizer\Base\AbstractAwareNormalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class ProductAwareNormalizer extends AbstractAwareNormalizer
{
    function getSupportedClass(): string
    {
        return Product::class;
    }

    protected function afterNormalize(array &$data)
    {
        if (isset($data["productTags"])) {
            $data["productTags"] = preg_split("~,~", $data['productTags']);
        }
        if (isset($data["previewImg"])) {
            $data['previewImgUrl'] = $this->getImageUrlPrefix().$data['previewImg'];
        }
    }

}
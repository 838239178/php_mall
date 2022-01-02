<?php


namespace App\Serializer\Normalizer;


use App\Entity\Brand;

class BrandAwareNormalizer extends Base\AbstractAwareNormalizer
{

    function getSupportedClass(): string
    {
        return Brand::class;
    }

    protected function afterNormalize(array &$data)
    {
        if (isset($data['logo'])) {
            $data['logoUrl'] = $this->getImageUrlPrefix().$data['logo'];
        }
    }


}
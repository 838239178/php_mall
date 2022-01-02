<?php


namespace App\Serializer\Normalizer;


use App\Entity\UserInfo;
use App\Serializer\Normalizer\Base\AbstractAwareNormalizer;

class UserInfoAwareNormalizer extends AbstractAwareNormalizer
{
    function getSupportedClass(): string
    {
        return UserInfo::class;
    }

    protected function afterNormalize(array &$data)
    {
        if (isset($data['avatar'])) {
            $data['avatarUrl'] = $this->getImageUrlPrefix().$data['avatar'];
        }
    }
}
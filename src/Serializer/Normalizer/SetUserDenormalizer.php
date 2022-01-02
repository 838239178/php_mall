<?php


namespace App\Serializer\Normalizer;

use App\Entity\Address;
use App\Entity\Collection;
use App\Entity\Comments;
use App\Entity\ShopCar;
use App\Entity\UserInfo;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class SetUserDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const SET_USER_ALREADY_DENORMALIZED = "set_user_flag_denormalized";
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function denormalize($data, string $type, string $format = null, array $context = []): object
    {
        $context[self::SET_USER_ALREADY_DENORMALIZED] = true;
        $object = $this->denormalizer->denormalize($data, $type, $format, $context);
        /** @var UserInfo $user */
        $user = $this->security->getUser();
        $object->setUser($user);
        return $object;
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (isset($context[self::SET_USER_ALREADY_DENORMALIZED])) {
            return false;
        }
        return $type == Address::class
            || $type == ShopCar::class
            || $type == Collection::class
            || $type == Comments::class;
    }

}
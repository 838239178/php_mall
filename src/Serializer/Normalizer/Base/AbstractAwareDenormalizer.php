<?php


namespace App\Serializer\Normalizer\Base;


use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

abstract class AbstractAwareDenormalizer extends AbstractSerializer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    protected function afterDenormalize(object& $data) {
    }
    protected function beforeDenormalize(array& $data){
    }
    abstract function getSupportedClass(): string;

    private function getUniqueContextKey(): string {
        return hash("md5", AbstractAwareDenormalizer::class.$this->getSupportedClass());
    }
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        if (isset($context[$this->getUniqueContextKey()])) {
            return false;
        }
        return $type === $this->getSupportedClass();
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $context[$this->getUniqueContextKey()] = true;
        $this->beforeDenormalize($data);
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
        $this->afterDenormalize($obj);
        return $obj;
    }


}
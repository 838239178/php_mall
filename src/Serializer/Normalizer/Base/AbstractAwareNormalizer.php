<?php


namespace App\Serializer\Normalizer\Base;


use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

abstract class AbstractAwareNormalizer extends AbstractSerializer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    protected function afterNormalize(array& $data) {
    }
    protected function beforeNormalize(object& $data){
    }
    abstract function getSupportedClass(): string;

    private function getUniqueContextKey(): string {
        return hash("md5", AbstractAwareNormalizer::class.$this->getSupportedClass());
    }
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (isset($context[$this->getUniqueContextKey()])) {
            return false;
        }
        $supportedClass = $this->getSupportedClass();
        return $data instanceof ($supportedClass);
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $context[$this->getUniqueContextKey()] = true;
        $this->beforeNormalize($object);
        $data = $this->normalizer->normalize($object, $format, $context);
        $this->afterNormalize($data);
        return $data;
    }


}
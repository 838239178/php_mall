<?php


namespace App\Util;

use ApiPlatform\Core\JsonLd\Serializer\ItemNormalizer;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\Strategy\Impl\LowerCamelCaseNamingStrategy;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class HttpUtils
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param Request $request HttpFoundation
     * @param string $className A::class
     * @return bool|object if wrappers failed returns false else object instance
     */
    public function wrapperRequest(Request $request, string $className): bool|object
    {
        return self::wrapperDict($request->request->all(), $className);
    }

    public function wrapperArray(array $arr, string $elementType): bool|ArrayCollection
    {
        $coll = new ArrayCollection();
        foreach ($arr as $item) {
            if ($elementType != "default") {
                $res = self::wrapperDict($item, $elementType);
                if ($res === false) {
                    return false;
                }
                $coll->add($res);
            } else {
                $coll->add($item);
            }
        }
        return $coll;
    }

    public function wrapperDict(array $dict, string $className): bool|object
    {
        try {
            $ref = new ReflectionClass($className);
            $obj = $ref->newInstance();
            foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $mt) {
                $mName = $mt->getName();
                $matches = [];
                if (preg_match('/set([A-Z])(\w*)/', $mName, $matches)) {
                    $propName = strtolower($matches[1]) . $matches[2];
                    $propValue = array_key_exists($propName, $dict) ? $dict[$propName] : null;

                    if ($propValue != null) {
                        $attrs = $ref->getProperty($propName)->getAttributes(WrapperOption::class);
                        $attr = count($attrs) > 0 ? $attrs[0]->newInstance() : new WrapperOption();

                        if ($attr->type == WrapperOption::ARRAY) {
                            if ($attr->elementType == null) {
                                throw new InvalidArgumentException($propName . " is array but didn't provide element's type");
                            }
                            $propValue = self::wrapperArray($propValue, $attr->elementType);
                        } else if ($attr->type != WrapperOption::DEFAULT) {
                            $propValue = self::wrapperDict($propValue, $attr->type);
                        }
                    }

                    $mt->invokeArgs($obj, array($propValue));
                }
            }
        } catch (ReflectionException) {
            return false;
        }
        return $obj;
    }

    public function wrapperErrors(ConstraintViolationListInterface $errors): JsonResponse
    {
        $data = [
            'success' => false,
            'message' => []
        ];
        foreach ($errors as $e) {
            $data['message'][$e->getPropertyPath()] = $e->getMessage();
        }
        return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
    }

    public function wrapperFail(string $message, int $responseCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(data: [
            'success' => false,
            'message' => $message
        ], status: $responseCode);
    }

    public function wrapperSuccess($data = ["message"=>"请求成功"], int $responseCode = Response::HTTP_OK): JsonResponse
    {
        if (!is_array($data)) {
            $data = $this->normalizer->normalize($data, "jsonld", []);
        }
        return new JsonResponse(
            data: $data,
            status: $responseCode,
        );
    }
}
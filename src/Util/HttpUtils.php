<?php


namespace App\Util;

use App\Serializer\JsonSerializer;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class HttpUtils
{
    /**
     * @param Request $request HttpFoundation
     * @param string $className A::class
     * @return bool|object if wrappers failed returns false else object instance
     */
    public static function wrapperRequest(Request $request, string $className): bool|object
    {
        return self::wrapperDict($request->request->all(), $className);
    }

    public static function wrapperArray(array $arr, string $elementType): bool|ArrayCollection
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

    public static function wrapperDict(array $dict, string $className): bool|object
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
                                throw new InvalidArgumentException($propName . "is array but didn't provide element type");
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

    public static function wrapperErrors(ConstraintViolationListInterface $errors): JsonResponse
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

    public static function wrapperFail(string $message, int $responseCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return new JsonResponse(data: [
            'success' => false,
            'message' => $message
        ], status: $responseCode);
    }

    public static function wrapperSuccess($data = [], string $message = "请求成功", int $responseCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(
            data: JsonSerializer::getInstance()->serialize([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], 'json'),
            status: $responseCode, json: true
        );
    }
}
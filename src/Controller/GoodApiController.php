<?php


namespace App\Controller;


use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\GoodDTO;
use App\Entity\Good;
use App\Repository\GoodRepository;
use App\Repository\ProductRepository;
use App\Serializer\JsonSerializer;
use App\Util\HttpUtils;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\DeserializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/api/goods", name: "api_good")]
class GoodApiController extends AbstractController
{
    private GoodRepository $goodRepository;
    private ProductRepository $productRepository;
    private HttpUtils $httpUtils;

    public function __construct(GoodRepository $goodRepository, ProductRepository $productRepository, HttpUtils $httpUtils)
    {
        $this->goodRepository = $goodRepository;
        $this->productRepository = $productRepository;
        $this->httpUtils = $httpUtils;
    }


    #[Route(path: "/getByProduct/{pid}", name: "_getByProduct", methods: ["GET"])]
    public function getByProduct(int $pid): Response
    {
        $prod = $this->productRepository->find($pid);
        $goods = $this->goodRepository->findBy(['product'=>$prod]);
        return count($goods) > 0 ?
            $this->httpUtils->wrapperSuccess($goods) : $this->httpUtils->wrapperFail("找不到");
    }

    #[Route(path: "/{pid}/stock", name: "_getStock", methods: ["GET"])]
    public function getStock(): Response
    {

    }

    #[Route(path: "/getByProp/{pid}", name: "_getByProp", methods: ["POST"])]
    public function getByProp(int $pid, Request $request): Response
    {
        /** @var array $opts */
        $opts = $request->get("options");
        $optsCollection = $this->httpUtils->wrapperArray($opts, GoodDTO::class);
        $prod = $this->productRepository->find($pid);
        $res = $this->goodRepository->getGoodByProp($prod, $optsCollection);
        if ($res != null) {
            $res->setProduct(null);
            return $this->httpUtils->wrapperSuccess($res);
        } else {
            return  $this->httpUtils->wrapperFail("NotFoundResource", Response::HTTP_NOT_FOUND);
        }
    }
}
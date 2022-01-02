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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/api/goods", name: "api_good")]
class GoodApiController extends AbstractController
{
    private GoodRepository $goodRepository;
    private ProductRepository $productRepository;

    public function __construct(GoodRepository $goodRepository, ProductRepository $productRepository)
    {
        $this->goodRepository = $goodRepository;
        $this->productRepository = $productRepository;
    }


    #[Route(path: "/getByProduct/{pid}", name: "_getByProduct", methods: ["GET"])]
    public function getByProduct(int $pid): Response
    {
        $prod = $this->productRepository->find($pid);
        $goods = $this->goodRepository->findBy(['product'=>$prod]);
        return count($goods) > 0 ?
            HttpUtils::wrapperSuccess($goods) : HttpUtils::wrapperFail("找不到");
    }

    #[Route(path: "/getByProp/{pid}", name: "_getByProp", methods: ["POST"])]
    public function getByProp(int $pid, Request $request): Response
    {
        /** @var array $opts */
        $opts = $request->get("options");
        $optsCollection = HttpUtils::wrapperArray($opts, GoodDTO::class);
        $prod = $this->productRepository->find($pid);
        $res = $this->goodRepository->getGoodByProp($prod, $optsCollection);
        if ($res != null) {
            $res->setPropKeys(null);
            $res->setProduct(null);
            return HttpUtils::wrapperSuccess($res);
        } else {
            return  HttpUtils::wrapperFail("找不到");
        }
    }
}
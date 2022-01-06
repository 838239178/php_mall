<?php


namespace App\Controller;


use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Util\HttpUtils;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductIntroApiController extends AbstractController
{
    public function __invoke(string $productId, ProductRepository $productRepository, HttpUtils $httpUtils): object
    {
        try {
            return $httpUtils->wrapperDict($productRepository->createQueryBuilder("p")
                ->select("p.introPage","p.productId")
                ->where("p.productId = :id")
                ->setParameter("id", $productId)
                ->getQuery()->getSingleResult(),Product::class);
        } catch (NoResultException | NonUniqueResultException $e) {
            throw new ItemNotFoundException(code: 404);
        }
    }
}
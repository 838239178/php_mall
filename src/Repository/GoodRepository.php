<?php


namespace App\Repository;

use App\DTO\GoodDTO;
use App\Entity\Good;
use App\Entity\GoodPropKey;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Good|null find($id, $lockMode = null, $lockVersion = null)
 * @method Good|null findOneBy(array $criteria, array $orderBy = null)
 * @method Good[]    findAll()
 * @method Good[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Good::class);
    }

    /**
     * @param Product $product
     * @param ArrayCollection<GoodDTO> $values
     * @return Good|null
     */
    public function getGoodByProp(Product $product, ArrayCollection $values): ?Good
    {
        $goods = $this->findBy(['product' => $product]);
        foreach ($goods as $g) {
            $matched = $g->getPropKeys()->forAll(
                function ($idx) use ($values, $g) {
                    /** @var GoodPropKey $item */
                    $item = $g->getPropKeys()->get($idx);
                    return $values->filter(
                            fn(GoodDTO $dto) => $dto->getPropKeyId() == $item->getKey()->getKeyId() && $dto->getValue() == $item->getValue()
                        )->count() > 0;
                }
            );
            if ($matched) {
                return $g;
            }
        }
        return null;
    }
}
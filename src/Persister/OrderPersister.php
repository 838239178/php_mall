<?php


namespace App\Persister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\GoodPropKey;
use App\Entity\Orders;
use App\Entity\OrdersDetail;
use App\Exception\InvalidPersistException;
use App\Util\SetterUtil;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\PersisterException;

class OrderPersister implements ContextAwareDataPersisterInterface
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Orders;
    }

    /**
     * @param Orders $data
     * @param array $context
     * @throws InvalidPersistException
     */
    public function persist($data, array $context = [])
    {
        /** @var OrdersDetail $detail */
        foreach ($data->getDetails() as $detail) {
            $detail->setOrders($data);
            $good = $detail->getGood();
            if ($good->getStock() >= $detail->getProductSize()) {
                $good->setStock($good->getStock() - $detail->getProductSize());
            } else {
                throw new InvalidPersistException("stock of this good doesn't enough");
            }
            $descArray = $good->getPropKeys()
                ->map(fn(GoodPropKey $gp)=> $gp->getValue())
                ->toArray();
            $detail->setProductName($good->getProduct()->getProductName());
            $detail->setProductPrice($good->getSalePrice());
            $detail->setGoodDesc(join(',', $descArray));
            $data->setTotalPrice($data->getTotalPrice() + $good->getSalePrice());
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
    }
}
<?php


namespace App\Persister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\GoodPropKey;
use App\Entity\Orders;
use App\Entity\OrdersDetail;
use App\Entity\Shop;
use App\Entity\UserInfo;
use App\Exception\InvalidPersistException;
use App\Strategy\OrderCalcStrategy;
use App\Util\SetterUtil;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\PersisterException;
use Symfony\Component\Security\Core\Security;

class OrderPersister implements ContextAwareDataPersisterInterface
{

    private EntityManagerInterface $entityManager;
    private Security $security;
    private OrderCalcStrategy $calculator;

    public function __construct(EntityManagerInterface $entityManager, Security $security, OrderCalcStrategy $calculator)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->calculator = $calculator;
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
        /** @var UserInfo $user */
        $user = $this->security->getUser();
        $details = $data->getDetails();
        /** @var Shop $shop */
        $shop = $details->first()->getGood()->getProduct()->getShop();
        //init order
        $data->setUser($user);
        $data->setShop($shop);
        $data->setExpressPrice($shop->getExpressPrice());
        $data->setExpressAddress($shop->getExpressAddr());
        $data->setExpressName($shop->getExpressName());
        $data->setTotalPrice($this->calculator->calc($data));
        /** @var OrdersDetail $detail */
        foreach ($details as $detail) {
            $good = $detail->getGood();
            $curProd = $good->getProduct();
            //shop checking
            if ($curProd->getShop()->getShopId() != $shop->getShopId()) {
                throw new InvalidPersistException("Invalid another shop's good");
            }
            //stock checking
            if ($good->getStock() >= $detail->getProductSize()) {
                $good->setStock($good->getStock() - $detail->getProductSize());
            } else {
                throw new InvalidPersistException("Stock of this good doesn't enough");
            }
            //order's detail initialization
            $descArray = $good->getPropKeys()
                ->map(fn(GoodPropKey $gp)=> $gp->getValue())
                ->toArray();
            $detail->setProductName($curProd->getProductName());
            $detail->setProductPrice($good->getSalePrice());
            $detail->setGoodDesc(join(',', $descArray));
            $detail->setOrders($data);
        }
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
    }
}
<?php


namespace App\EventSubscriber;


use App\Entity\Good;
use App\Entity\GoodPropKey;
use App\Entity\ProductPropKey;
use App\Entity\PropKey;
use App\Repository\ProductRepository;
use App\Util\SetterUtil;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use JetBrains\PhpStorm\Pure;
use KaiGrassnick\SnowflakeBundle\Generator\SnowflakeGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GoodPersistSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    /**
     * ProductPersistSubscriber constructor.
     * @param LoggerInterface $logger
     */
    #[Pure] public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => "setGoodPropKeyAndProductOptionValue",
            BeforeEntityUpdatedEvent::class => "setGoodPropKeyAndProductOptionValue"
        ];
    }

    public function setGoodPropKeyAndProductOptionValue(AbstractLifecycleEvent $event) {
        $good = $event->getEntityInstance();
        if ($good instanceof Good) {
            $product = $good->getProduct();
            //update lowest price
            if ($product->getLowestPrice() == null || $product->getLowestPrice() > $good->getSalePrice()) {
                $product->setLowestPrice($good->getSalePrice());
            }
            //update option values of product's prop keys
            /** @var GoodPropKey $item */
            foreach ($good->getPropKeys()->toArray() as $item) {
                if ($item->getGood() === null) {
                    $item->setGood($good);
                }
                $key = $item->getKey();
                /** @var ProductPropKey $findPk */
                $findPk = $product->getPropKeys()
                    ->filter(fn(ProductPropKey $pk)=> $pk->getPropKey()->getKeyId() === $key->getKeyId())
                    ->first();
                if ($findPk != null) {
                    $opt = $findPk->getOptionValues();
                    //stored if not exits this value
                    if(!str_contains($opt, $item->getValue())) {
                        $findPk->setOptionValues($opt . "," . $item->getValue());
                    }
                }
            }
        }
    }

}
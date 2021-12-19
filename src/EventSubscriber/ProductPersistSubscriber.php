<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\ProductPropKey;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use KaiGrassnick\SnowflakeBundle\Generator\SnowflakeGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPersistSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private SnowflakeGenerator $snowflakeGenerator;
    /**
     * ProductPersistSubscriber constructor.
     * @param LoggerInterface $logger
     */
    #[Pure] public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->snowflakeGenerator = new SnowflakeGenerator();
    }


    public function setPropKeyRelation(AbstractLifecycleEvent $event)
    {
        $product = $event->getEntityInstance();
        //todo set product shop id from user context
        if ($product instanceof Product) {
            $product->getPropKeys()
                ->map(fn(ProductPropKey $i)=>$i->setProduct($product));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AbstractLifecycleEvent::class => 'setPropKeyRelation',
        ];
    }
}

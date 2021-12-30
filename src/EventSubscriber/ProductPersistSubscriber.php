<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\ProductPropKey;
use App\Entity\UserInfo;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use JetBrains\PhpStorm\Pure;
use KaiGrassnick\SnowflakeBundle\Generator\SnowflakeGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;

class ProductPersistSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private Security $security;


    public function __construct(LoggerInterface $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }


    public function setPropKeyRelation(AbstractLifecycleEvent $event)
    {
        $product = $event->getEntityInstance();
        if ($product instanceof Product) {
            $this->logger->info("init product prop keys and shop");
            /** @var UserInfo $user */
            $user = $this->security->getUser();
            $product->setShop($user->getShop());
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

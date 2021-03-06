<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Product;
use App\Entity\ProductPropKey;
use App\Entity\UserInfo;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use JetBrains\PhpStorm\Pure;
use KaiGrassnick\SnowflakeBundle\Generator\SnowflakeGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
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


    public function beforeModifyProduct(AbstractLifecycleEvent $event)
    {
        $product = $event->getEntityInstance();
        if ($product instanceof Product) {
            if ($product->getProductStatus() == "deployed") {
                $product->setDeployTime(new DateTime());
            }
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
            BeforeEntityPersistedEvent::class => 'beforeModifyProduct',
            BeforeEntityUpdatedEvent::class => 'beforeModifyProduct',
        ];
    }
}

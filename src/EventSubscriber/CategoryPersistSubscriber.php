<?php


namespace App\EventSubscriber;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Event\AbstractLifecycleEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategoryPersistSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    /**
     * CategoryPersistSubscriber constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    public function setLevel(AbstractLifecycleEvent $event) {
        $cat = $event->getEntityInstance();
        if ($cat instanceof Category) {
            $this->logger->info("[category] before modify: ".$cat->getCategoryName());
            if ($cat->getParent() == null) {
                $cat->setCategoryLevel(1);
            } else if ($cat->getParent()->getCategoryLevel() < 3){
                $cat->setCategoryLevel($cat->getParent()->getCategoryLevel() + 1);
            } else {
                $cat->setParent(null);
                $cat->setCategoryLevel(1);
                $this->logger->warning("Not valid category's parent, max level of parent is 2 but 3 get!");
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'setLevel',
            BeforeEntityUpdatedEvent::class => 'setLevel'
        ];
    }
}
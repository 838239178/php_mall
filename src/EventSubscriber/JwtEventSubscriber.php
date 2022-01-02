<?php


namespace App\EventSubscriber;


use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtEventSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $this->logger->info("onAuthenticationSuccessResponse");
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['id'] = $user->getUserIdentifier();
        $data['role'] = $user->getRoles()[0];

        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class=>'onAuthenticationSuccessResponse'
        ];
    }
}
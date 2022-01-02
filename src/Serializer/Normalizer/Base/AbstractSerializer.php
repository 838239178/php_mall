<?php


namespace App\Serializer\Normalizer\Base;


use App\Entity\UserInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSerializer
{
    private RequestStack $requestStack;
    private Security $security;
    private string $imagePath;

    public function __construct(RequestStack $requestStack, Security $security, string $imagePath)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->imagePath = $imagePath;
    }

    protected function getCurrentRequest(): Request {
        return $this->requestStack->getCurrentRequest();
    }

    protected function getCurrentUser() :UserInfo {
        /** @var UserInfo $usr */
        $usr = $this->security->getUser();
        return $usr;
    }

    protected function getImageUrlPrefix(): string {
        return $this->getSchemeHost().$this->imagePath;
    }

    protected function getSchemeHost():string {
        return $this->getCurrentRequest()->getSchemeAndHttpHost();
    }
}
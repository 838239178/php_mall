<?php


namespace App\Controller;


use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Entity\Address;
use App\Entity\UserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[AsController]
class AddressApiController extends AbstractController
{

    public function __invoke(Address $data, Security $security, EntityManagerInterface $em): Address
    {
        if ($data == null) {
            throw new ItemNotFoundException("Not Found");
        }
        /** @var UserInfo $userInfo */
        $userInfo = $security->getUser();
        $userInfo->setDefaultAddress($data);
        $em->flush();
        return $data;
    }
}
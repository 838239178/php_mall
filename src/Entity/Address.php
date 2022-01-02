<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Consts\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Address
 *
 * @ORM\Table(name="address", indexes={@ORM\Index(name="fk_address_user_info_1", columns={"user_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get','post'],
    itemOperations: [
        'get',
        'patch'=>[
            'security_post_denormalize' =>'previous_object.getUserId() == user.getUserId()'
        ],
        'delete'=>[
            'security_post_denormalize' =>'previous_object.getUserId() == user.getUserId()'
        ]
    ],
    attributes: [
        'security'=>"is_granted('".Role::USER."')",
        "pagination_items_per_page" => 10
    ],
    denormalizationContext: ['groups'=>['addr:write']],
    normalizationContext: ['groups'=>['addr:read']]
)]
class Address
{
    /**
     * @var int
     *
     * @ORM\Column(name="address_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['addr:read'])]
    private $addressId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consignee", type="string", length=32, nullable=true, options={"comment"="收货人姓名"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $consignee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consignee_phone", type="string", length=32, nullable=true, options={"comment"="收货人电话"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $consigneePhone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="province", type="string", length=32, nullable=true, options={"comment"="省"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $province;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=32, nullable=true, options={"comment"="市"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="country", type="string", length=32, nullable=true, options={"comment"="县区"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $country;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street", type="string", length=100, nullable=true, options={"comment"="详细地址"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $street;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true, options={"comment"="默认地址 1-true"})
     */
    #[Groups(['addr:read','addr:write'])]
    #[NotBlank]
    private $isDefault = '0';

    /**
     * @var \UserInfo
     *
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    #[Groups(['addr:read'])]
    private $user;

    public function getAddressId(): ?string
    {
        return $this->addressId;
    }

    public function getConsignee(): ?string
    {
        return $this->consignee;
    }

    public function setConsignee(?string $consignee): self
    {
        $this->consignee = $consignee;

        return $this;
    }

    public function getConsigneePhone(): ?string
    {
        return $this->consigneePhone;
    }

    public function setConsigneePhone(?string $consigneePhone): self
    {
        $this->consigneePhone = $consigneePhone;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getUser(): ?UserInfo
    {
        return $this->user;
    }

    public function setUser(?UserInfo $user): self
    {
        $this->user = $user;

        return $this;
    }


}

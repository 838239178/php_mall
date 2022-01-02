<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Consts\Role;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * ShopCar
 *
 * @ORM\Table(name="shop_car", indexes={@ORM\Index(name="index_create_time", columns={"create_time"}), @ORM\Index(name="fk_shop_car_good_1", columns={"good_id"}), @ORM\Index(name="index_shop_id", columns={"shop_id"}), @ORM\Index(name="fk_shop_car_user_info_1", columns={"user_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
        'get',
        'delete'=>[
            'security_post_denormalize' =>'previous_object.getUserId() == user.getUserId()'
        ],
        'patch'=>[
            'denormalizationContext'=>['groups'=>['car:update']],
            'security_post_denormalize' =>'previous_object.getUserId() == user.getUserId()'
        ]
    ],
    attributes: [
        "security"=>"is_grant('".Role::USER.")",
        "pagination_items_per_page" => 10
    ],
    denormalizationContext: ['groups'=>['car:write']], normalizationContext: ['groups'=>['car:read']]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['createTime'=>'DESC']
)]
class ShopCar
{
    /**
     * @var int
     *
     * @ORM\Column(name="car_id", type="bigint", nullable=false, options={"comment"="购物车id"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['car:read'])]
    private $carId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="shop_id", type="bigint", nullable=true, options={"comment"="卖家id"})
     */
    #[Groups(['car:read'])]
    private $shopId;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="加入时间"})
     */
    #[Groups(['car:read'])]
    private $createTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="product_size", type="integer", nullable=true, options={"comment"="购买数量"})
     */
    #[Groups(['car:read','car:write', 'car:update'])]
    #[GreaterThan(0)]
    private $productSize;

    /**
     * @var Good
     *
     * @ORM\ManyToOne(targetEntity="Good")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="good_id", referencedColumnName="good_id")
     * })
     */
    #[Groups(['car:read','car:write', 'car:update'])]
    #[NotBlank]
    #[ApiProperty(writableLink: false)]
    private $good;

    /**
     * @var UserInfo
     *
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    public function getCarId(): ?string
    {
        return $this->carId;
    }

    public function getShopId(): ?string
    {
        return $this->shopId;
    }

    public function setShopId(?string $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setCreateTime(?\DateTimeInterface $createTime): self
    {
        $this->createTime = $createTime;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(?bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getProductSize(): ?int
    {
        return $this->productSize;
    }

    public function setProductSize(?int $productSize): self
    {
        $this->productSize = $productSize;

        return $this;
    }

    public function getGood(): ?Good
    {
        return $this->good;
    }

    public function setGood(?Good $good): self
    {
        $this->good = $good;

        return $this;
    }

    public function getUser(): ?UserInfo
    {
        return $this->user;
    }

    public function setUser(UserInfo $user): self
    {
        $this->user = $user;

        return $this;
    }
}

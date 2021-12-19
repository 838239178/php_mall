<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Shop
 *
 * @ORM\Table(name="shop", indexes={@ORM\Index(name="fk_shop_user_info_1", columns={"user_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get']
)]
class Shop
{
    /**
     * @var int
     *
     * @ORM\Column(name="shop_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['prod:read'])]
    private $shopId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shop_icon", type="string", length=100, nullable=true)
     */
    #[Groups(['prod:read'])]
    private $shopIcon;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shop_name", type="string", length=30, nullable=true)
     */
    #[Groups(['prod:read'])]
    private $shopName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="shop_desc", type="string", length=100, nullable=true, options={"comment"="店铺描述"})
     */
    private $shopDesc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="alipay_id", type="string", length=64, nullable=true, options={"comment"="支付宝商户ID"})
     */
    private $alipayId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="wepay_id", type="string", length=64, nullable=true, options={"comment"="微信支付商户ID"})
     */
    private $wepayId;

    /**
     * @var UserInfo
     *
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private UserInfo $user;

    /**
     * @ORM\Column(name="express_addr", type="string", length=255, nullable=true)
     */
    private string $expressAddr;

    /**
     * @ORM\Column(name="express_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private float $expressPrice;

    /**
     * @ORM\Column(name="express_name", type="decimal", precision=10, scale=2, nullable=true)
     */
    private string $expressName;

    public function getShopId(): ?string
    {
        return $this->shopId;
    }

    public function getShopIcon(): ?string
    {
        return $this->shopIcon;
    }

    public function setShopIcon(?string $shopIcon): self
    {
        $this->shopIcon = $shopIcon;

        return $this;
    }

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function setShopName(?string $shopName): self
    {
        $this->shopName = $shopName;

        return $this;
    }

    public function getShopDesc(): ?string
    {
        return $this->shopDesc;
    }

    public function setShopDesc(?string $shopDesc): self
    {
        $this->shopDesc = $shopDesc;

        return $this;
    }

    public function getAlipayId(): ?string
    {
        return $this->alipayId;
    }

    public function setAlipayId(?string $alipayId): self
    {
        $this->alipayId = $alipayId;

        return $this;
    }

    public function getWepayId(): ?string
    {
        return $this->wepayId;
    }

    public function setWepayId(?string $wepayId): self
    {
        $this->wepayId = $wepayId;

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

    /**
     * @return string
     */
    public function getExpressAddr(): string
    {
        return $this->expressAddr;
    }

    /**
     * @param string $expressAddr
     */
    public function setExpressAddr(string $expressAddr): void
    {
        $this->expressAddr = $expressAddr;
    }

    /**
     * @return float
     */
    public function getExpressPrice(): float
    {
        return $this->expressPrice;
    }

    /**
     * @param float $expressPrice
     */
    public function setExpressPrice(float $expressPrice): void
    {
        $this->expressPrice = $expressPrice;
    }

    /**
     * @return string
     */
    public function getExpressName(): string
    {
        return $this->expressName;
    }

    /**
     * @param string $expressName
     */
    public function setExpressName(string $expressName): void
    {
        $this->expressName = $expressName;
    }


}

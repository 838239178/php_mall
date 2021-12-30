<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * OrdersDetail
 *
 * @ORM\Table(name="orders_detail", indexes={@ORM\Index(name="good_id", columns={"good_id"}), @ORM\Index(name="search_prod_name", columns={"product_name"}), @ORM\Index(name="fk_detail_orders_orders_1", columns={"orders_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    denormalizationContext: ['groups'=>["order:write"]],
    normalizationContext: ['groups'=>['order:read']]
)]
class OrdersDetail
{
    /**
     * @var int
     *
     * @ORM\Column(name="detail_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['order:read'])]
    private $detailId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="product_id", type="bigint", nullable=true, options={"comment"="商品编号"})
     */
    private $productId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_name", type="string", length=40, nullable=true, options={"comment"="冗余-商品名"})
     */
    #[Groups(['order:read'])]
    private $productName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="product_size", type="integer", nullable=true, options={"comment"="商品数量"})
     */
    #[Groups(['order:read','order:write'])]
    #[GreaterThan(0)]
    private $productSize;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_price", type="decimal", precision=10, scale=2, nullable=true, options={"comment"="商品价格"})
     */
    #[Groups(['order:read'])]
    private $productPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_img", type="string", length=255, nullable=true, options={"comment"="预览图地址"})
     */
    private $productImg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="good_desc", type="string", length=255, nullable=true, options={"comment"="sku描述，下单时通过good的values构建"})
     */
    private $goodDesc;

    /**
     * @var Orders
     *
     * @ORM\ManyToOne(targetEntity="Orders", inversedBy="details")
     * @ORM\JoinColumn(name="orders_id", referencedColumnName="orders_id")
     */
    #[Groups(['order:read'])]
    #[ApiProperty(readableLink: false)]
    private $orders;

    /**
     * @var Good
     *
     * @ORM\ManyToOne(targetEntity="Good", cascade={"refresh"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="good_id", referencedColumnName="good_id")
     * })
     */
    #[Groups(['order:read','order:write'])]
    #[ApiProperty(writableLink: false)]
    #[NotBlank]
    private $good;

    public function getDetailId(): ?string
    {
        return $this->detailId;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setProductId(?string $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): self
    {
        $this->productName = $productName;

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

    public function getProductPrice(): ?string
    {
        return $this->productPrice;
    }

    public function setProductPrice(?string $productPrice): self
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    public function getProductImg(): ?string
    {
        return $this->productImg;
    }

    public function setProductImg(?string $productImg): self
    {
        $this->productImg = $productImg;

        return $this;
    }

    public function getGoodDesc(): ?string
    {
        return $this->goodDesc;
    }

    public function setGoodDesc(?string $goodDesc): self
    {
        $this->goodDesc = $goodDesc;

        return $this;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): self
    {
        $this->orders = $orders;

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

    /**
     * @param int $detailId
     */
    public function setDetailId(int $detailId): void
    {
        $this->detailId = $detailId;
    }

    public function __toString(): string
    {
        return "[".$this->good->getProduct()->getProductName()."(".$this->goodDesc.")".":".$this->productPrice."元"."x".$this->productSize."]";
    }

}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\GoodApiController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Good
 *
 * @ORM\Table(name="good", indexes={@ORM\Index(name="product_id", columns={"product_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    denormalizationContext: ['groups'=>['']],
    normalizationContext: ['groups'=>['good:read']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['product'=>'exact']
)]
class Good
{
    /**
     * @var int
     *
     * @ORM\Column(name="good_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['good:read'])]
    private $goodId;

    /**
     * @var float|null
     *
     * @ORM\Column(name="original_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    #[Groups(['good:read'])]
    private $originalPrice;

    /**
     * @var float|null
     *
     * @ORM\Column(name="sale_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    #[Groups(['good:read'])]
    private $salePrice;

    /**
     * @var int|null
     *
     * @ORM\Column(name="stock", type="integer", nullable=true)
     */
    #[Groups(['good:read'])]
    private $stock;

    /**
     * @var Product|null
     *
     * @ORM\ManyToOne(targetEntity="Product",cascade={"remove","persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     * })
     */
    #[Groups(['good:read'])]
    #[ApiProperty(readableLink: false)]
    private $product;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\GoodPropKey", mappedBy="good")
     */
    #[Groups(['good:read'])]
    #[ApiProperty(readableLink: true)]
    private $propKeys;

    /**
     * Good constructor.
     */
    #[Pure] public function __construct()
    {
        $this->propKeys = new ArrayCollection();
    }

    public function setGoodId(int $id) {
        $this->goodId = $id;
    }

    public function getGoodId(): ?string
    {
        return $this->goodId;
    }

    public function getOriginalPrice(): ?float
    {
        return $this->originalPrice;
    }

    public function setOriginalPrice(?float $originalPrice): self
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }

    public function getSalePrice(): ?float
    {
        return $this->salePrice;
    }

    public function setSalePrice(?float $salePrice): self
    {
        $this->salePrice = $salePrice;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropKeys(): \Doctrine\Common\Collections\Collection
    {
        return $this->propKeys;
    }

    /**
     * @param ?\Doctrine\Common\Collections\Collection $propKeys
     */
    public function setPropKeys(?\Doctrine\Common\Collections\Collection $propKeys): void
    {
        $this->propKeys = $propKeys;
    }


}

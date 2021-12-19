<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Filter\ForceQueryFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="fk_product_shop_1", columns={"shop_id"}), @ORM\Index(name="fk_product_brand", columns={"brand_id"}), @ORM\Index(name="index_create_time", columns={"create_time"}), @ORM\Index(name="search_product", columns={"product_name", "product_tags"}), @ORM\Index(name="fk_product_category_1", columns={"category_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: ['groups'=>['prod:read']]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['lowestPrice','deployTime'=>'ASC']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['productName'=>'partial', 'productTags'=>'word_start']
)]
#[ApiFilter(RangeFilter::class, properties: ['lowestPrice'])]
#[ApiFilter(ForceQueryFilter::class, arguments: ['forceWhere' => ['productStatus'=>'deployed']])]
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['prod:read'])]
    private $productId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_name", type="string", length=40, nullable=true, options={"comment"="商品名"})
     */
    #[Groups(['prod:read'])]
    private $productName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_desc", type="string", length=100, nullable=true, options={"comment"="商品描述"})
     */
    #[Groups(['prod:read'])]
    private $productDesc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_pic", type="string", length=500, nullable=true, options={"comment"="商品图册地址，逗号分隔，最多五个"})
     */
    private $productPic;

    /**
     * @var string|null
     *
     * @ORM\Column(name="preview_img", type="string", length=150, nullable=true, options={"comment"="预览图地址"})
     */
    #[Groups(['prod:read'])]
    private $previewImg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="product_status", type="string", length=30, nullable=true, options={"default"="undeployed","comment"="状态 deployed undeployed invalid"})
     */
    #[Groups(['prod:read'])]
    private $productStatus = 'undeployed';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deploy_time", type="datetime", nullable=true, options={"comment"="上架时间"})
     */
    #[Groups(['prod:read'])]
    private $deployTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="intro_page", type="string", length=255, nullable=true)
     */
    private $introPage;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lowest_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    #[Groups(['prod:read'])]
    private $lowestPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="product_tags", type="string", length=150, nullable=false, options={"comment"="逗号分隔"})
     */
    #[Groups(['prod:read'])]
    private $productTags;

    /**
     * @var \Brand
     *
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="brand_id", referencedColumnName="brand_id")
     * })
     */
    #[Groups(['prod:read'])]
    private $brand;

    /**
     * @var ?Category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     * })
     */
    #[Groups(['prod:read'])]
    private $category;

    /**
     * @var \Shop
     *
     * @ORM\ManyToOne(targetEntity="Shop")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shop_id", referencedColumnName="shop_id")
     * })
     */
    #[Groups(['prod:read'])]
    #[ApiProperty(readableLink: true)]
    private $shop;

    /**
     * @var \Doctrine\Common\Collections\Collection<ProductPropKey>
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\ProductPropKey",
     *     mappedBy="product",
     *     fetch="EXTRA_LAZY",
     *     cascade={"persist"}
     * )
     */
    #[Groups(['prod:read'])]
    #[ApiProperty(readableLink: true)]
    private $propKeys;

    /**
     * @var \Doctrine\Common\Collections\Collection<Good>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Good", mappedBy="product", fetch="EXTRA_LAZY")
     */
    #[Groups(['prod:read'])]
    #[ApiProperty(readableLink: false)]
    private $goods;

    public function getProductId(): ?string
    {
        return $this->productId;
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

    public function getProductDesc(): ?string
    {
        return $this->productDesc;
    }

    public function setProductDesc(?string $productDesc): self
    {
        $this->productDesc = $productDesc;

        return $this;
    }

    public function getProductPic(): ?string
    {
        return $this->productPic;
    }

    public function setProductPic(?string $productPic): self
    {
        $this->productPic = $productPic;

        return $this;
    }

    public function getPreviewImg(): ?string
    {
        return $this->previewImg;
    }

    public function setPreviewImg(?string $previewImg): self
    {
        $this->previewImg = $previewImg;

        return $this;
    }

    public function getProductStatus(): ?string
    {
        return $this->productStatus;
    }

    public function setProductStatus(?string $productStatus): self
    {
        $this->productStatus = $productStatus;

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

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setCreateTime(?\DateTimeInterface $createTime): self
    {
        $this->createTime = $createTime;

        return $this;
    }

    public function getDeployTime(): ?\DateTimeInterface
    {
        return $this->deployTime;
    }

    public function setDeployTime(?\DateTimeInterface $deployTime): self
    {
        $this->deployTime = $deployTime;

        return $this;
    }

    public function getIntroPage(): ?string
    {
        return $this->introPage;
    }

    public function setIntroPage(?string $introPage): self
    {
        $this->introPage = $introPage;

        return $this;
    }

    public function getLowestPrice(): ?float
    {
        return $this->lowestPrice;
    }

    public function setLowestPrice(?float $lowestPrice): self
    {
        $this->lowestPrice = $lowestPrice;

        return $this;
    }

    public function getProductTags(): ?string
    {
        return $this->productTags;
    }

    public function setProductTags(string|array $productTags): self
    {
        if (is_array($productTags)) {
            $this->productTags = implode(separator: ",", array: $productTags);
        } else {
            $this->productTags = $productTags;
        }

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function getPropKeys(): \Doctrine\Common\Collections\Collection
    {
        if ($this->propKeys == null) {
            $this->propKeys = new ArrayCollection();
        }
        return $this->propKeys;
    }

    public function setPropKeys(\Doctrine\Common\Collections\Collection $propKeys): void
    {
        $this->propKeys = $propKeys;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoods(): \Doctrine\Common\Collections\Collection
    {
        return $this->goods;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $goods
     */
    public function setGoods(\Doctrine\Common\Collections\Collection $goods): void
    {
        $this->goods = $goods;
    }
}

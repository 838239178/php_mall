<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Brand
 *
 * @ORM\Table(name="brand", indexes={@ORM\Index(name="search_brand", columns={"brand_name", "brand_desc"})})
 * @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    denormalizationContext: ['groups'=>['']],
    normalizationContext: ['groups'=>['read']]
)]
#[ApiFilter(SearchFilter::class, properties: ['brandName'=>'partial', 'category'=>'exact'])]
class Brand
{
    /**
     * @var int
     *
     * @ORM\Column(name="brand_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['read','product:simple'])]
    private $brandId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="brand_name", type="string", length=32, nullable=true, options={"comment"="品牌英文名称"})
     */
    #[Groups(['read','product:simple'])]
    private $brandName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="brand_desc", type="string", length=64, nullable=true, options={"comment"="品牌中文名称/描述"})
     */
    #[Groups(['read'])]
    private $brandDesc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true, options={"comment"="logo url"})
     */
    #[Groups(['read','product:simple'])]
    private $logo;

    /**
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumns ({
     *     @ORM\JoinColumn(name="category_id",referencedColumnName="category_id")
     * })
     */
    #[Groups(['read'])]
    private $category;

    public function getBrandId(): ?string
    {
        return $this->brandId;
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function setBrandName(?string $brandName): self
    {
        $this->brandName = $brandName;

        return $this;
    }

    public function getBrandDesc(): ?string
    {
        return $this->brandDesc;
    }

    public function setBrandDesc(?string $brandDesc): self
    {
        $this->brandDesc = $brandDesc;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $cate): self
    {
        $this->category = $cate;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getBrandName();
    }

}

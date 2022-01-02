<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Category
 *
 * @ORM\Table(name="category", indexes={@ORM\Index(name="search_category", columns={"category_name", "category_desc"}), @ORM\Index(name="index_create_time", columns={"create_time"})})
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    denormalizationContext: ['groups'=>['']],
    normalizationContext: ['groups'=>['read']]
)]
#[ApiFilter(SearchFilter::class, properties: ['categoryLevel'=>'exact', 'parent'=>'exact'])]
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="category_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['read','product:simple'])]
    private $categoryId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="category_name", type="string", length=32, nullable=true, options={"comment"="分类名称"})
     */
    #[Groups(['read','product:simple'])]
    private $categoryName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="category_desc", type="string", length=32, nullable=true, options={"comment"="分类描述"})
     */
    #[Groups(['read'])]
    private $categoryDesc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="创建时间"})
     */
    #[Groups(['read'])]
    private $createTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="category_level", type="integer", nullable=true, options={"comment"="分类级别1、2、3"})
     */
    #[Groups(['read'])]
    private $categoryLevel;

    /**
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(name="parent_cid",referencedColumnName="category_id")
     */
    #[Groups(['read'])]
    #[ApiProperty(readableLink: false)]
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection<Category>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Category",mappedBy="parent")
     */
    #[Groups(['read'])]
    private \Doctrine\Common\Collections\Collection $children;

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(?string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getCategoryDesc(): ?string
    {
        return $this->categoryDesc;
    }

    public function setCategoryDesc(?string $categoryDesc): self
    {
        $this->categoryDesc = $categoryDesc;

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

    public function getCategoryLevel(): ?int
    {
        return $this->categoryLevel;
    }

    public function setCategoryLevel(?int $categoryLevel): self
    {
        $this->categoryLevel = $categoryLevel;

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $p): self
    {
        $this->parent = $p;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren(): \Doctrine\Common\Collections\Collection
    {
        return $this->children;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $children
     */
    public function setChildren(\Doctrine\Common\Collections\Collection $children): void
    {
        $this->children = $children;
    }

    public function __toString(): string
    {
        return $this->getCategoryName();
    }

}

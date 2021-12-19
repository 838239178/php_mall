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
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Collection
 *
 * @ORM\Table(name="collection",
 *     indexes={
 *     @ORM\Index(name="index_user_id", columns={"user_id"}),
 *     @ORM\Index(name="index_product_id", columns={"product_id"}),
 *     @ORM\Index(name="index_shop_id", columns={"shop_id"}),
 *     @ORM\Index(name="index_create_time", columns={"create_time"}),
 *     @ORM\Index(name="fk_collection_category_1", columns={"category_id"}),
 *     @ORM\Index(name="fk_coll_prod", columns={"product_id"}),
 * })
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete'],
    attributes: ['security'=>"is_granted('".Role::USER."')"],
    denormalizationContext: ['groups'=>['write']],
    normalizationContext: ['groups'=>['read']]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['createTime'=>'DESC'],
    arguments: ['orderParameterName' => 'order']
)]
class Collection
{
    /**
     * @var int
     *
     * @ORM\Column(name="coll_id", type="bigint", nullable=false, options={"comment"="收藏id"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['read'])]
    private $collId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="category_id", type="bigint", nullable=true, options={"comment"="分类id"})
     */
    #[Groups(['read'])]
    private $categoryId;

    /**
     * @var Product|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", cascade={"remove"})
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id", nullable=false)
     */
    #[Groups(['read','write'])]
    #[NotBlank]
    private $product;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="收藏时间"})
     */
    #[Groups(['read'])]
    private $createTime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="coll_status", type="integer", nullable=true, options={"comment"="状态 0-正常 1-失效"})
     */
    #[Groups(['read'])]
    private $collStatus = '0';

    /**
     * @var UserInfo
     *
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    #[Groups(['read'])]
    private $user;

    public function getCollId(): ?string
    {
        return $this->collId;
    }

    public function getCategoryId(): ?string
    {
        return $this->categoryId;
    }

    public function setCategoryId(?string $categoryId): self
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $productId): self
    {
        $this->product = $productId;

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

    public function getCollStatus(): ?int
    {
        return $this->collStatus;
    }

    public function setCollStatus(?int $collStatus): self
    {
        $this->collStatus = $collStatus;

        return $this;
    }

    public function getUser(): UserInfo
    {
        return $this->user;
    }

    public function setUser(?UserInfo $user): self
    {
        $this->user = $user;

        return $this;
    }


}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Extension\NotLimitUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Comments
 *
 * @ORM\Table(name="comments", indexes={@ORM\Index(name="fk_comments_product_1", columns={"product_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 */
#[NotLimitUser]
#[ApiResource(
    collectionOperations: ['get','post'],
    itemOperations: ['get','delete'],
    denormalizationContext: ['groups'=>['write']],
    normalizationContext: ['groups'=>['read']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['user'=>'exact', 'product'=>'exact']
)]
#[ApiFilter(
    OrderFilter::class,
    properties: ['createTime'=>'DESC'],
    arguments: ['orderParameterName' => 'order']
)]
class Comments
{
    /**
     * @var int
     *
     * @ORM\Column(name="comments_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['read'])]
    private $commentsId;

    /**
     * @var Comments|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Comments", inversedBy="children")
     */
    #[Groups(['read','write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    private $parent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="good_desc", type="string", nullable=false, options={"comment"="购买的sku型号"})
     */
    #[Groups(['read'])]
    private $goodDesc;

    /**
     * @var UserInfo|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserInfo", cascade={"remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     */
    #[Groups(['read'])]
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", nullable=false)
     */
    #[Groups(['read','write'])]
    #[NotBlank]
    private string $content;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="收藏时间"})
     */
    #[Groups(['read'])]
    private $createTime;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", cascade={"remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id", nullable=false)
     * })
     */
    #[Groups(['read','write'])]
    #[NotBlank]
    private $product;

    /**
     * @var \Doctrine\Common\Collections\Collection<Comments>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="parent", cascade={"remove"})
     */
    #[Groups(['read','write'])]
    private \Doctrine\Common\Collections\Collection $children;

    public function getCommentsId(): ?string
    {
        return $this->commentsId;
    }

    public function getGoodDesc(): ?string
    {
        return $this->goodDesc;
    }

    public function setGoodDesc(?string $goodId): self
    {
        $this->goodDesc = $goodId;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

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
    public function getChildren(): \Doctrine\Common\Collections\Collection
    {
        return $this->children;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $replies
     */
    public function setChildren(\Doctrine\Common\Collections\Collection $replies): void
    {
        $this->children = $replies;
    }

    /**
     * @return Comments
     */
    public function getParent(): Comments
    {
        return $this->parent;
    }

    /**
     * @param Comments $parentComment
     */
    public function setParent(Comments $parentComment): void
    {
        $this->parent = $parentComment;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}

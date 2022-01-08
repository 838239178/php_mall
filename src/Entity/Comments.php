<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Consts\Role;
use App\Extension\NotLimitUser;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Comments
 *
 * @ORM\Table(name="comments", indexes={@ORM\Index(name="fk_comments_product_1", columns={"product_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
#[NotLimitUser]
#[ApiResource(
    collectionOperations: [
        'get',
        'post'=>[
            'security'=>"is_granted('".Role::USER."')",
            'security_post_denormalize' =>'object.getOrdersDetail().getComment() == null and object.getOrdersDetail().getOrders().getUser().getUserId() == user.getUserId()'
        ]
    ],
    itemOperations: [
        'get',
        'delete'=>[
            'security'=>"is_granted('".Role::USER."')",
            'security_post_denormalize' =>'previous_object.getUser().getUserId() == user.getUserId()'
        ]
    ],
    attributes: [
        "pagination_items_per_page" => 20
    ],
    denormalizationContext: ['groups'=>['comment:write']], normalizationContext: ['groups'=>['comment:read']]
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
    #[Groups(['comment:read'])]
    private $commentsId;

    /**
     * @var Comments|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Comments", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="comments_id", name="reply_cid", nullable=true)
     */
    #[Groups(['read','comment:write'])]
    #[ApiProperty(readableLink: false, writableLink: false)]
    private $parent;

    /**
     * @var UserInfo|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserInfo", cascade={"remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     */
    #[Groups(['comment:read'])]
    #[ApiProperty(readableLink: true, writableLink: false)]
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", nullable=false)
     */
    #[Groups(['comment:read','comment:write'])]
    #[NotBlank]
    private string $content;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="收藏时间"})
     */
    #[Groups(['comment:read'])]
    private $createTime;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id", nullable=false)
     * })
     */
    #[Groups(['comment:read','comment:write'])]
    #[NotBlank]
    #[ApiProperty(readableLink: false, writableLink: false)]
    private $product;

    /**
     * @var \Doctrine\Common\Collections\Collection<Comments>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="parent", cascade={"remove"})
     */
    #[Groups(['comment:read'])]
    #[ApiProperty(writable: false, readableLink: true)]
    private \Doctrine\Common\Collections\Collection $children;

    /**
     * @var OrdersDetail
     *
     * @ORM\OneToOne(targetEntity="App\Entity\OrdersDetail", inversedBy="comment")
     * @ORM\JoinColumn(referencedColumnName="detail_id", name="detail_id", nullable=false)
     */
    #[Groups(['comment:read','comment:write'])]
    #[ApiProperty(readableLink: true, writableLink: false)]
    private OrdersDetail $ordersDetail;

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

    public function getUser(): ?UserInfo
    {
        return $this->user;
    }

    public function setUser(?UserInfo $userId): self
    {
        $this->user = $userId;

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

    /**
     * @ORM\PrePersist()   //每次在commit前都会执行这个函数，达到自动更新创建时间和更新时间
     */
    public function PrePersist(){
        if($this->getCreateTime()==null){
            $this->setCreateTime(date_create());
        }
    }

    /**
     * @return DateTime|null
     */
    public function getCreateTime(): ?DateTime
    {
        return $this->createTime;
    }

    /**
     * @param DateTime|null $createTime
     */
    public function setCreateTime(?DateTime $createTime): void
    {
        $this->createTime = $createTime;
    }

    /**
     * @return OrdersDetail
     */
    public function getOrdersDetail(): OrdersDetail
    {
        return $this->ordersDetail;
    }

    /**
     * @param OrdersDetail $ordersDetail
     */
    public function setOrdersDetail(OrdersDetail $ordersDetail): void
    {
        $this->ordersDetail = $ordersDetail;
    }
}

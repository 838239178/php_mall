<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Consts\Role;
use App\Util\SetterUtil;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\Collection as Coll;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Orders
 *
 * @ORM\Table(name="orders", indexes={@ORM\Index(name="index_express_id", columns={"express_id"}), @ORM\Index(name="index_pay_id", columns={"pay_id"}), @ORM\Index(name="index_create_time", columns={"create_time"}), @ORM\Index(name="index_shop_id", columns={"shop_id"}), @ORM\Index(name="fk_orders_user_info_1", columns={"user_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
#[ApiResource(
    collectionOperations: [
        'get',
        'post',
    ],
    itemOperations: [
        'get',
    ],
    attributes: [
        'security'=>"is_granted('".Role::USER."')",
        "pagination_items_per_page" => 5
    ],
    denormalizationContext: ['groups'=>['order:write']],
    normalizationContext: ['groups'=>['order:read']]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: ['ordersStatus'=>'exact', 'keywords'=>'partial']
)]
#[ApiFilter(RangeFilter::class, properties: ['createTime'])]
#[ApiFilter(
    OrderFilter::class,
    properties: ['createTime'=>'DESC']
)]
class Orders
{
    /**
     * @var int
     *
     * @ORM\Column(name="orders_id", type="bigint", nullable=false, options={"comment"="订单编号"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['order:read','patch:state'])]
    private $ordersId;

    /**
     * @var Shop|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Shop")
     * @ORM\JoinColumn(referencedColumnName="shop_id", name="shop_id")
     */
    #[Groups('order:read')]
    private $shop;

    /**
     * @var float|null
     *
     * @ORM\Column(name="total_price", type="decimal", precision=10, scale=2, nullable=true, options={"comment"="订单总价格"})
     */
    #[Groups('order:read')]
    private $totalPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="orders_status", type="string", length=30, nullable=true, options={"comment"="状态 wait_pay-未支付 wait_express-待发货 wait_receive-待收货 wait_draw_back-待退货 canceled-已取消 finished-已完成"})
     */
    #[Groups(['order:read','patch:state'])]
    private $ordersStatus = 'wait_pay';

    /**
     * @var int|null
     *
     * @ORM\Column(name="pay_id", type="bigint", nullable=true, options={"comment"="支付单号"})
     */
    #[Groups('order:read')]
    private $payId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pay_type", type="string", length=32, nullable=true, options={"comment"="支付类型 银行卡 微信 支付宝"})
     */
    #[Groups('order:read')]
    private $payType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="express_id", type="string", length=64, nullable=true, options={"comment"="快递单号"})
     */
    #[Groups('order:read')]
    private $expressId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="express_name", type="string", length=32, nullable=true, options={"comment"="快递公司名"})
     */
    #[Groups('order:read')]
    private $expressName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="express_address", type="string", length=255, nullable=true, options={"comment"="发货地址"})
     */
    #[Groups('order:read')]
    private $expressAddress;

    /**
     * @ORM\Column(name="keywords", type="string", length=500)
     */
    private string $keywords;

    /**
     * @var float|null
     *
     * @ORM\Column(name="express_price", type="decimal", precision=10, scale=2, nullable=true, options={"comment"="运费价格"})
     */
    #[Groups('order:read')]
    private $expressPrice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true, options={"comment"="用户收货地址"})
     */
    #[Groups(['order:write','order:read'])]
    #[NotBlank]
    private $address;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="pay_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="支付时间"})
     */
    #[Groups(['order:read','patch:state'])]
    private $payTime;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="finish_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="收货时间"})
     */
    #[Groups(['order:read','patch:state'])]
    private $finishTime;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="express_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="发货时间"})
     */
    #[Groups(['order:read','patch:state'])]
    private $expressTime;

    /**
     * @var DateTime|null
     *C
     * @ORM\Column(name="create_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="下单时间"})
     */
    #[Groups('order:read')]
    private $createTime;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="refund_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="申请退款时间"})
     */
    #[Groups(['order:read'])]
    private $refundTime;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="cancel_time", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP","comment"="订单取消时间"})
     */
    #[Groups(['order:read'])]
    private $cancelTime;

    /**
     * @var UserInfo
     *
     * @ORM\ManyToOne(targetEntity="UserInfo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    #[Groups('order:read')]
    private $user;

    /**
     * @var Coll<OrdersDetail>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\OrdersDetail", mappedBy="orders", cascade={"persist", "remove"})
     */
    #[Groups(['order:write','order:read'])]
    #[Assert\Count(min: 1)]
    private Coll $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
    }


    public function getOrdersId(): ?string
    {
        return $this->ordersId;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shopId): self
    {
        $this->shop = $shopId;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(?float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getOrdersStatus(): ?string
    {
        return $this->ordersStatus;
    }

    public function setOrdersStatus(?string $ordersStatus): self
    {
        $this->ordersStatus = $ordersStatus;

        return $this;
    }

    public function getPayId(): ?string
    {
        return $this->payId;
    }

    public function setPayId(?string $payId): self
    {
        $this->payId = $payId;

        return $this;
    }

    public function getPayType(): ?string
    {
        return $this->payType;
    }

    public function setPayType(?string $payType): self
    {
        $this->payType = $payType;

        return $this;
    }

    public function getExpressId(): ?string
    {
        return $this->expressId;
    }

    public function setExpressId(?string $expressId): self
    {
        $this->expressId = $expressId;

        return $this;
    }

    public function getExpressName(): ?string
    {
        return $this->expressName;
    }

    public function setExpressName(?string $expressName): self
    {
        $this->expressName = $expressName;

        return $this;
    }

    public function getExpressAddress(): ?string
    {
        return $this->expressAddress;
    }

    public function setExpressAddress(?string $expressAddress): self
    {
        $this->expressAddress = $expressAddress;

        return $this;
    }

    public function getExpressPrice(): ?float
    {
        return $this->expressPrice;
    }

    public function setExpressPrice(?float $expressPrice): self
    {
        $this->expressPrice = $expressPrice;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPayTime(): ?DateTimeInterface
    {
        return $this->payTime;
    }

    public function setPayTime(?DateTimeInterface $payTime): self
    {
        $this->payTime = $payTime;

        return $this;
    }

    public function getFinishTime(): ?DateTimeInterface
    {
        return $this->finishTime;
    }

    public function setFinishTime(?DateTimeInterface $finishTime): self
    {
        $this->finishTime = $finishTime;

        return $this;
    }

    public function getExpressTime(): ?DateTimeInterface
    {
        return $this->expressTime;
    }

    public function setExpressTime(?DateTimeInterface $expressTime): self
    {
        $this->expressTime = $expressTime;

        return $this;
    }

    public function getCreateTime(): ?DateTimeInterface
    {
        return $this->createTime;
    }

    public function setCreateTime(?DateTimeInterface $createTime): self
    {
        $this->createTime = $createTime;

        return $this;
    }

    public function getRefundTime(): ?DateTimeInterface
    {
        return $this->refundTime;
    }

    public function setRefundTime(?DateTimeInterface $refundTime): self
    {
        $this->refundTime = $refundTime;

        return $this;
    }

    public function getUser(): ?UserInfo
    {
        return $this->user;
    }

    public function setUser(?UserInfo $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Coll<OrdersDetail>
     */
    public function getDetails(): Coll
    {
        return $this->details;
    }

    /**
     * @param Coll|array $details
     */
    public function setDetails(Coll|array $details): void
    {
        SetterUtil::setCollection($this->details, $details);
    }

    /**
     * @param int $ordersId
     */
    public function setOrdersId(int $ordersId): void
    {
        $this->ordersId = $ordersId;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
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
     * @ORM\PreFlush()
     */
    public function PreFlush() {
        if($this->getOrdersStatus() === 'wait_receive') {
            $this->setExpressTime(date_create());
            $this->setExpressId("SF999999999999");
        }
        if($this->getOrdersStatus() === 'canceled') {
            $this->setCancelTime(date_create());
        }
        if($this->getOrdersStatus() === 'wait_express') {
            $this->setPayTime(date_create());
        }
        if($this->getOrdersStatus() === 'finished') {
            $this->setFinishTime(date_create());
        }
    }

    /**
     * @return DateTime|null
     */
    public function getCancelTime(): ?DateTime
    {
        return $this->cancelTime;
    }

    /**
     * @param DateTime|null $cancelTime
     */
    public function setCancelTime(?DateTime $cancelTime): void
    {
        $this->cancelTime = $cancelTime;
    }

}

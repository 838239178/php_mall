<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ProductPropKey
 *
 * @ORM\Table(name="product_prop_key", indexes={@ORM\Index(name="fk_product_prop_key_product_1", columns={"product_id"}), @ORM\Index(name="prop_key_id", columns={"prop_key_id", "product_id"}), @ORM\Index(name="IDX_9FFAD56321D80491", columns={"prop_key_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
)]
class ProductPropKey
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['prod:read','car:read'])]
    private $id;

    /**
     * @var array|null
     *
     * @ORM\Column(name="option_values", type="json", nullable=true, options={"comment"="所有可选值 用逗号分隔的数组"})
     */
    #[Groups(['prod:read','car:read'])]
    #[ApiProperty(description: "包含可选值的<b>数组</b>")]
    private ?array $optionValues;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="propKeys")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     * })
     */
    private $product;

    /**
     * @var PropKey
     *
     * @ORM\ManyToOne(targetEntity="PropKey")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="prop_key_id", referencedColumnName="key_id")
     * })
     */
    #[Groups(['prod:read', 'car:read','good:read'])]
    #[ApiProperty(readableLink: true)]
    private $propKey;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOptionValues(): ?array
    {
        if ($this->optionValues == null) return [];
        return $this->optionValues;
    }

    public function setOptionValues(?array $optionValues): self
    {
        $this->optionValues = $optionValues;

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

    public function getPropKey(): ?PropKey
    {
        return $this->propKey;
    }

    public function setPropKey(?PropKey $propKey): self
    {
        $this->propKey = $propKey;

        return $this;
    }

    public function __toString(): string
    {
        return "[".$this->propKey->getKeyName().":".join(",",$this->optionValues)."]";
    }

}

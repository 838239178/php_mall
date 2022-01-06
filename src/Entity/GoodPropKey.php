<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * GoodPropKey
 *
 * @ORM\Table(name="good_prop_key", indexes={@ORM\Index(name="idx_value", columns={"value"}), @ORM\Index(name="fk_good_prop_value_prop_key_1", columns={"key_id"}), @ORM\Index(name="good_id", columns={"good_id"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: ['groups'=>['read']]
)]
class GoodPropKey
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['read'])]
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=20, nullable=false, options={"comment"="对应的值，来自[product_prop_key]option_values"})
     */
    #[Groups(['read','good:read', 'car:read','order:read'])]
    private $value;

    /**
     * @var Good
     *
     * @ORM\ManyToOne(targetEntity="Good")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="good_id", referencedColumnName="good_id")
     * })
     */
    #[ApiProperty(readableLink: false)]
    #[Groups(['read'])]
    private $good;

    /**
     * @var PropKey
     *
     * @ORM\ManyToOne(targetEntity="PropKey")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="key_id", referencedColumnName="key_id")
     * })
     */
    #[Groups(['read','good:read'])]
    #[ApiProperty(readableLink: true)]
    private ?PropKey $key;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    public function getKey(): ?PropKey
    {
        return $this->key;
    }

    public function setKey(?PropKey $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function __toString(): string
    {
        return "[".$this->key->getKeyName().":".$this->value."]";
    }

}

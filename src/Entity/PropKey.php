<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * PropKey
 *
 * @ORM\Table(name="prop_key", uniqueConstraints={@ORM\UniqueConstraint(name="idx_key_name", columns={"key_name"})}, indexes={@ORM\Index(name="search_key_name", columns={"key_name"})})
 * @ORM\Entity
 */
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get'],
    normalizationContext: ['groups'=>'read']
)]
class PropKey
{
    /**
     * @var int
     *
     * @ORM\Column(name="key_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['read','prod:read'])]
    private $keyId;

    /**
     * @var Category|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="category_id")
     */
    #[Groups(['read'])]
    #[ApiProperty(readableLink: false)]
    private $category;

    /**
     * @var string|null
     *
     * @ORM\Column(name="key_name", type="string", length=32, nullable=true)
     */
    #[Groups(['read','good:read', 'prod:read'])]
    private $keyName = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="create_uid", type="bigint", nullable=true, options={"comment"="创建者"})
     */
    private $createUid;

    public function getKeyId(): ?string
    {
        return $this->keyId;
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

    public function getKeyName(): ?string
    {
        return $this->keyName;
    }

    public function setKeyName(?string $keyName): self
    {
        $this->keyName = $keyName;

        return $this;
    }

    public function getCreateUid(): ?string
    {
        return $this->createUid;
    }

    public function setCreateUid(?string $createUid): self
    {
        $this->createUid = $createUid;

        return $this;
    }
}

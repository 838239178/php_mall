<?php


namespace App\DTO;


use App\Util\WrapperOption;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;

class GoodDTO
{
    #[NotBlank]
    private ?int $propKeyId;
    #[NotBlank]
    private ?string $value;

    public function getPropKeyId(): ?int
    {
        return $this->propKeyId;
    }

    public function setPropKeyId(?int $propKeyId): void
    {
        $this->propKeyId = $propKeyId;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }
}
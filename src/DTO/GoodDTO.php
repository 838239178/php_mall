<?php


namespace App\DTO;


use App\Util\WrapperOption;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotBlank;

class GoodDTO
{
    #[NotBlank]
    #[WrapperOption(type: WrapperOption::DEFAULT)]
    private ?int $propKeyId;

    #[NotBlank]
    #[WrapperOption(type: WrapperOption::DEFAULT)]
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
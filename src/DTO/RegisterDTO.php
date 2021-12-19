<?php


namespace App\DTO;


use Symfony\Component\Validator\Constraints\NotBlank;

class RegisterDTO
{
    #[NotBlank]
    private ?string $username;
    #[NotBlank]
    private ?string $email;
    #[NotBlank]
    private ?string $password;
    #[NotBlank]
    private ?string $emailCode;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }


    public function getEmailCode(): ?string
    {
        return $this->emailCode;
    }

    public function setEmailCode(?string $emailCode): void
    {
        $this->emailCode = $emailCode;
    }
}
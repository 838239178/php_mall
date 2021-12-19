<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Consts\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * UserInfo
 *
 * @ORM\Table(name="user_info")
 * @ORM\Entity
 * @method string getUserIdentifier()
 */
#[ApiResource(
    collectionOperations: [],
    itemOperations: ['get', 'patch'],
    denormalizationContext: ['groups'=>['user:patch']],
    normalizationContext: ['groups'=>['user:read']]
)]
class UserInfo implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="KaiGrassnick\DoctrineSnowflakeBundle\Generator\SnowflakeGenerator")
     */
    #[Groups(['user:read'])]
    private $userId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=true, options={"comment"="唯一用户名"})
     */
    #[Groups(['user:read'])]
    #[NotBlank]
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=32, nullable=true, options={"comment"="邮箱"})
     */
    #[Groups(['user:read'])]
    #[NotBlank]
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true, options={"comment"="密码 md5hash"})
     */
    #[NotBlank]
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true, options={"comment"="md5hash salt"})
     */
    private $salt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avatar", type="string", length=255, nullable=true, options={"comment"="头像地址"})
     */
    #[Groups(['user:read','user:patch'])]
    #[NotBlank]
    private $avatar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nick_name", type="string", length=32, nullable=true, options={"comment"="昵称"})
     */
    #[Groups(['user:read','user:patch'])]
    #[NotBlank]
    private $nickName;


    /**
     * @ORM\Column(type="json", name="roles", length=255, nullable=true)
     */
    private array $roles = [];

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(?string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }


    public function getRoles(): array
    {
        if(count($this->roles) == 0) {
            $this->roles[] = Role::USER;
        }
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials()
    {
    }
}

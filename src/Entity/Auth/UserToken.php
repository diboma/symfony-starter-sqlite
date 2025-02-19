<?php

namespace App\Entity\Auth;

use App\Entity\User\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Auth\UserTokenRepository;

#[ORM\Entity(repositoryClass: UserTokenRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserToken
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private int $id;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
  private \DateTimeImmutable $createdAt;

  public function __construct(
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userToken')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private User $user,

    #[ORM\Column(type: 'string', length: 255)]
    private string $token,
  ) {}

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->createdAt = new \DateTimeImmutable();
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getUser(): User
  {
    return $this->user;
  }

  public function setUser(User $user): static
  {
    $this->user = $user;
    return $this;
  }

  public function getToken(): string
  {
    return $this->token;
  }

  public function setToken(string $token): static
  {
    $this->token = $token;
    return $this;
  }

  public function getCreatedAt(): \DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;
    return $this;
  }
}

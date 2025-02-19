<?php

namespace App\Entity\Auth;

use App\Repository\Auth\UserTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTokenRepository::class)]
class UserToken
{
  #[ORM\Id]
  #[ORM\Column(length: 255, unique: true)]
  private ?string $email = null;

  #[ORM\Column(length: 255)]
  private ?string $token = null;

  #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
  private \DateTimeImmutable $createdAt;

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->createdAt = new \DateTimeImmutable();
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(string $email): static
  {
    $this->email = $email;

    return $this;
  }

  public function getToken(): ?string
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

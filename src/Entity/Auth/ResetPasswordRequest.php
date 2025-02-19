<?php

namespace App\Entity\Auth;

use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Auth\ResetPasswordRequestRepository;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
  use ResetPasswordRequestTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  /** @phpstan-var int */
  private int $id;

  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private User $user;

  public function __construct(User $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
  {
    $this->user = $user;
    $this->initialize($expiresAt, $selector, $hashedToken);
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getUser(): User
  {
    return $this->user;
  }
}

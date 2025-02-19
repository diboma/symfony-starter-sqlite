<?php

namespace App\Entity\User;

use App\Entity\Auth\UserToken;
use App\Entity\Product\Product;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  /** @phpstan-var int */
  private int $id;

  /**
   * @var list<string> The user roles
   */
  #[ORM\Column]
  private array $roles = ['ROLE_USER'];

  /**
   * @var string The hashed password
   */
  #[ORM\Column]
  private ?string $password = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $avatarUrl = null;

  #[ORM\OneToOne(targetEntity: UserToken::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
  private UserToken $userToken;

  #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
  private bool $email_verified = false;

  /**
   * @var Collection<int, Product>
   */
  #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'owner', orphanRemoval: true)]
  private Collection $products;

  public function __construct(
    #[ORM\Column(length: 255)]
    private string $firstName,

    #[ORM\Column(length: 255)]
    private ?string $lastName,

    #[ORM\Column(length: 180)]
    private string $email,

  ) {
    $this->products = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
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

  public function getUserToken(): UserToken
  {
    return $this->userToken;
  }

  public function setUserToken(UserToken $userToken): static
  {
    $this->userToken = $userToken;
    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   *
   * @return list<string>
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  /**
   * @param list<string> $roles
   */
  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  public function getFullName(): ?string
  {
    return $this->firstName . ' ' . $this->lastName;
  }

  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): static
  {
    $this->firstName = $firstName;

    return $this;
  }

  public function getLastName(): ?string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): static
  {
    $this->lastName = $lastName;

    return $this;
  }

  public function getAvatarUrl(): ?string
  {
    return $this->avatarUrl;
  }

  public function setAvatarUrl(?string $avatarUrl): static
  {
    $this->avatarUrl = $avatarUrl;

    return $this;
  }

  /**
   * @return Collection<int, Product>
   */
  public function getProducts(): Collection
  {
    return $this->products;
  }

  public function addProduct(Product $product): static
  {
    if (!$this->products->contains($product)) {
      $this->products->add($product);
      $product->setOwner($this);
    }

    return $this;
  }

  public function removeProduct(Product $product): static
  {
    if ($this->products->removeElement($product)) {
      // set the owning side to null (unless already changed)
      if ($product->getOwner() === $this) {
        $product->setOwner(null);
      }
    }

    return $this;
  }

  public function isEmailVerified(): ?bool
  {
    return $this->email_verified;
  }

  public function setEmailVerified(bool $email_verified): static
  {
    $this->email_verified = $email_verified;

    return $this;
  }
}

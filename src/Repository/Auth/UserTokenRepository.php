<?php

namespace App\Repository\Auth;

use App\Entity\User\User;
use App\Entity\Auth\UserToken;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<UserToken>
 */
class UserTokenRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, UserToken::class);
  }

  public function save(UserToken $userToken): void
  {
    $entityManager = $this->getEntityManager();
    $entityManager->persist($userToken);
    $entityManager->flush();
  }

  public function verifyToken(User $user, string $token): bool
  {
    $userToken = $this->findOneBy(['user' => $user]);

    if (!$userToken || $userToken->getToken() !== $token || $userToken->getCreatedAt() < new \DateTime('-1 day')) {
      return false;
    }

    $user->setEmailVerified(true);
    $this->delete($userToken);
    return true;
  }

  public function delete(UserToken $token): void
  {
    $entityManager = $this->getEntityManager();
    $entityManager->remove($token);
    $entityManager->flush();
  }
}

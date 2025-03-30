<?php

namespace App\Repository\Auth;

use App\Entity\Auth\UserToken;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserToken>
 */
class UserTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserToken::class);
    }

    public function save(UserToken $userToken, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($userToken);
        if ($flush) {
            $entityManager->flush();
        }
    }

    public function flush(): void
    {
        $entityManager = $this->getEntityManager();
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

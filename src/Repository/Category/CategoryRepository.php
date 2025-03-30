<?php

namespace App\Repository\Category;

use App\Entity\Product\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function save(Category $category, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($category);
        if ($flush) {
            $entityManager->flush();
        }
    }

    public function flush(): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }
}

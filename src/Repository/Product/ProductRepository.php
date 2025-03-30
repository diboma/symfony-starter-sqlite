<?php

namespace App\Repository\Product;

use App\Entity\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Paginator<Product>
     */
    public function paginate(int $currentPage = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.id', 'DESC')
            ->getQuery()
            ->setFirstResult(($currentPage - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query, true);
    }

    public function findByNameLike(string $searchTerm): array
    {
        return $this->findAll();
        return $this->createQueryBuilder('p')
            ->where('p.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%' . $searchTerm . '%')
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Product $product, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($product);
        if ($flush) {
            $entityManager->flush();
        }
    }

    public function flush(): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->flush();
    }

    public function delete(Product $product): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($product);
        $entityManager->flush();
    }
}

<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
    * @return Category[] The objects.
    * @psalm-return list<Category>
    */
    public function findAllComplete()
    {
        $qb = $this->getQueryBuilderFindComplete();

        return $qb->getQuery()->getResult();
    }

    public function findComplete($id): ?Category
    {
        $qb = $this->getQueryBuilderFindComplete();
        $qb ->where($qb->expr()->eq('i.id', ':id'))
            ->setParameter(':id', $id)
        ;

        return $qb->getQuery()->getResult();
    }

    private function getQueryBuilderFindComplete(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $qb ->leftJoin('c.subcategories', 's')->addSelect('s');

        return $qb;
    }
}
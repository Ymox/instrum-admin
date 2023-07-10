<?php

namespace App\Repository;

use App\Entity\AwardType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AwardType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AwardType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AwardType[]    findAll()
 * @method AwardType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AwardTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AwardType::class);
    }

    // /**
    //  * @return AwardType[] Returns an array of AwardType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AwardType
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

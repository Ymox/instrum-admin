<?php

namespace App\Repository;

use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Status|null find($id, $lockMode = null, $lockVersion = null)
 * @method Status|null findOneBy(array $criteria, array $orderBy = null)
 * @method Status[]    findAll()
 * @method Status[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Status::class);
    }

    public function findHierarchical($status)
    {
        $qb = $this->getQueryBuilderFindHierarchical($status);

        return $qb->getQuery()->getResult();
    }

    public function getQueryBuilderFindHierarchical($status)
    {
        $qb = $this->createQueryBuilder('s');
        $andX = $qb->expr()->andX(
            $qb->expr()->between('s.lft', 's2.lft', 's2.rgt'),
            $qb->expr()->between('s.rgt', 's2.lft', 's2.rgt')
        );
        if (is_array($status)) {
            $andX->add($qb->expr()->in('s2.id', ':id'));
        } else {
            $andX->add($qb->expr()->eq('s2.id', ':id'));
        }
        $qb ->join(\App\Entity\Status::class, 's2', Join::WITH, $andX)
            ->setParameter(':id', $status)
        ;

        return $qb;
    }
}

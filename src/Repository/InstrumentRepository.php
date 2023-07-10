<?php

namespace App\Repository;

use App\Entity\Instrument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Instrument|null find($id, $lockMode = null, $lockVersion = null)
 * @method Instrument|null findOneBy(array $criteria, array $orderBy = null)
 * @method Instrument[]    findAll()
 * @method Instrument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstrumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Instrument::class);
    }

    /**
    * @return Instrument[] The objects.
    * @psalm-return list<Instrument>
    */
    public function findAllComplete()
    {
        $qb = $this->getQueryBuilderFindComplete();

        return $qb->getQuery()->getResult();
    }

    public function findComplete($id): ?Instrument
    {
        $qb = $this->getQueryBuilderFindComplete();
        $qb ->where($qb->expr()->eq('i.id', ':id'))
            ->setParameter(':id', $id)
        ;

        return $qb->getQuery()->getResult();
    }
    /**
     *
     * @param array $criteria
     * @param ?array $orderBy
     * @param ?integer $limit
     * @param ?integer $offset
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function searchBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
    {
        $qb = $this->getQueryBuilderFindComplete();

        foreach ($criteria as $field => $value) {
            if ($value === null) {
                continue;
            }
            $marker = ':' . $field;
            $qb->andWhere($qb->expr()->eq($this->sanitizeField($field), $marker));
            $qb->setParameter($marker, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($this->sanitizeField($field), $direction);
        }

        if (null != $limit) {
            $qb->setMaxResults($limit);
        }
        if (null != $offset) {
            $qb->setFirstResult($offset);
        }

        $paginator = new Paginator($qb);

        return $paginator;
    }

    private function getQueryBuilderFindComplete(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('i');
        $qb ->innerJoin('i.cat', 'c')->addSelect('c')
            ->leftJoin('i.subcat', 's')->addSelect('s')
            ->leftJoin('i.recipient', 'r')->addSelect('r')
            ->innerJoin('i.property', 'p')->addSelect('p')
        ;

        return $qb;
    }

    private function sanitizeField($field)
    {
        switch ($field) {
            case 'property':
                $field = 'p.name';
                break;
            case 'recipient':
                $field = 'r.lastname';
                break;
            case 'cat':
                $field = 'c.name';
                break;
            case 'subcat':
                $field = 's.name';
                break;
            case 'number':
            case 'brand':
            case 'usable':
                $field = 'i.' . $field;
            default:
                break;
        }

        return $field;
    }
}
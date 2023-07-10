<?php

namespace App\Repository;

use App\Entity\AwardType;
use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function searchBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
    {
        $qb = $this->createQueryBuilder('m');
        $qb ->leftJoin('m.statuses', 's')->addSelect('s')
            ->leftJoin('m.relateds', 'r')->addSelect('r')
            ->leftJoin('m.cat', 'c')->addSelect('c')
            ->leftJoin('m.levels', 'l')->addSelect('l')
            ->leftJoin('m.levels', 'l2') // for filters
        ;

        if (!empty($criteria['statuses'])) {
            if (isset($criteria['statuses']['on']) && !$criteria['statuses']['on']->isEmpty()) {
                if ($criteria['statuses']['off']->isEmpty() && $criteria['statuses']['on']->count() == 1) {
                    $qb ->innerJoin('m.statuses', 's1')
                        ->innerJoin(\App\Entity\Status::class, 's2', Join::WITH, $qb->expr()->andX(
                            $qb->expr()->in('s2.id', ':statusesPositive'),
                            $qb->expr()->between('s1.lft', 's2.lft', 's2.rgt'),
                            $qb->expr()->between('s1.rgt', 's2.lft', 's2.rgt')
                        ))
                        ->setParameter(':statusesPositive', $criteria['statuses']['on'])
                    ;
                } else {
                    $qb ->andWhere($qb->expr()->isMemberOf(':statusesPositive', 'm.statuses'))
                        ->setParameter(':statusesPositive', $criteria['statuses']['on']);
                }
            }
            if (isset($criteria['statuses']['off']) && !$criteria['statuses']['off']->isEmpty()) {
                if ($criteria['statuses']['on']->isEmpty() && $criteria['statuses']['off']->count() == 1) {
                    $qb ->innerJoin('m.statuses', 's3')
                        ->innerJoin(\App\Entity\Status::class, 's4', Join::WITH, $qb->expr()->not($qb->expr()->andX(
                            $qb->expr()->in('s4.id', ':statusesNegative'),
                            $qb->expr()->between('s3.lft', 's4.lft', 's4.rgt'),
                            $qb->expr()->between('s3.rgt', 's4.lft', 's4.rgt')
                        )))
                        ->setParameter(':statusesNegative', $criteria['statuses']['off'])
                    ;
                } else {
                    $qb ->andWhere($qb->expr()->not($qb->expr()->isMemberOf(':statusesNegative', 'm.statuses')))
                        ->setParameter(':statusesNegative', $criteria['statuses']['off']);
                }
            }

            unset($criteria['statuses']);
        }

        foreach ($criteria as $field => $value) {
            if (empty($value) || $value->isEmpty()) {
                continue;
            }
            $marker = ':' . $field;
            if (is_string($value)) {
                if (preg_match('`^".*"$`', $value)) {
                    $value = trim($value, '"');
                } else if (preg_match('`^!`', $value)) {
                    $value = '%' . $value . '%';
                }
            }

            if (\is_iterable($value)) {
                $qb ->andWhere($qb->expr()->in($this->sanitizeField($field), $marker));
            } else {
                $value = trim($value, "  \t\v\0");
                $qb ->andWhere($qb->expr()->like($this->sanitizeField($field), $marker));
            }
            $qb ->setParameter($marker, $value);
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

    public function findNotAwarded(AwardType $awardType, int $difference = 1)
    {
        $qb = $this->createQueryBuilder('m');
        $qb ->leftJoin(AwardType::class, 'at1', Join::WITH, $qb->expr()->eq('at1.id', ':awardTypeId'))
            ->leftJoin(AwardType::class, 'at2', Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('at1.column', 'at2.column'),
                $qb->expr()->neq('at2.id', 'at1.id'),
                $qb->expr()->lt('at2.years', 'at1.years')
            ))
            ->leftJoin('at2.members', 'a2', Join::WITH, $qb->expr()->eq('a2.member', 'm.id'))
            ->leftJoin(AwardType::class, 'at3', Join::WITH, $qb->expr()->andX(
                $qb->expr()->eq('at1.column', 'at3.column'),
                $qb->expr()->neq('at2.id', 'at3.id'),
                $qb->expr()->lt('at3.years', 'at1.years'),
                $qb->expr()->gt('at3.years', 'at2.years')
            ))
            ->innerJoin('m.statuses', 's', Join::WITH, $qb->expr()->in('s.id', ':eligibleStatuses')) // more efficient than MEMBER OF
            ->leftJoin('m.awards', 'a')
            ->leftJoin('a.awardType', 'at', Join::WITH, $qb->expr()->neq('at.id', 'at1.id'))
            ->where($qb->expr()->isNull('at3.id'))
            //->andWhere($qb->expr()->in('s.id', 'at1.eligibleStatuses'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->between(
                    new Func('if', [$qb->expr()->eq('at1.column', $qb->expr()->literal('band')), 'm.band', 'm.scmv']),
                    $qb->expr()->diff($qb->expr()->diff(':year', 'at1.years'), ':difference'),
                    $qb->expr()->sum($qb->expr()->diff(':year', 'at1.years'), ':difference')
                ),
                $qb->expr()->between(
                    'a2.year',
                    $qb->expr()->diff($qb->expr()->diff(':year', $qb->expr()->diff('at1.years', 'at2.years')), ':difference'),
                    $qb->expr()->sum($qb->expr()->diff(':year', $qb->expr()->diff('at1.years', 'at2.years')), ':difference')
                )
            ))
            ->setParameter(':awardTypeId', $awardType->getId())
            ->setParameter(':eligibleStatuses', $awardType->getEligibleStatuses())
            ->setParameter(':year', (int) date('Y'))
            ->setParameter(':difference', $difference)
            ->orderBy(new Func('if', [$qb->expr()->eq('at1.column', $qb->expr()->literal('band')), 'm.band', 'm.scmv']), 'ASC')
        ;

        return $qb->getQuery()->getResult();
    }

    private function sanitizeField($field)
    {
        switch ($field) {
            case 'level':
                $field = 'l2.name';
                break;
            case 'statuses':
                $field = 's2.name';
                break;
            case 'cat':
                $field = 'c.name';
                break;
            case 'subcat':
                $field = 'sc.name';
                break;
            case 'lastname':
            case 'firstname':
            case 'email':
                $field = 'm.' . $field;
            default:
                break;
        }

        return $field;
    }
}

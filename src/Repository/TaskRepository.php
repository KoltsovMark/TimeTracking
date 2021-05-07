<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public const TOTAL_ALIAS = 'total';
    public const TOTAL_TIME_SPENT_ALIAS = 'total_time_spent';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findQueryByUser(User $user): Query
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->getQuery();
    }

    /**
     * @param User $user
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     *
     * @return Task[]
     */
    public function findByUserAndDateRange(User $user, DateTime $startDate = null, DateTime $endDate = null): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->addCriteria($this->createUserCriteria($user))
            ->addCriteria($this->createBetweenDateCriteria($startDate, $endDate))
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param User $user
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     *
     * @return array
     * @throws Query\QueryException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getStatisticsByUserAndDateRange(User $user, DateTime $startDate = null, DateTime $endDate = null): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select([
                'COUNT(t.id) AS ' . self::TOTAL_ALIAS,
                'SUM(t.timeSpent) AS ' . self::TOTAL_TIME_SPENT_ALIAS,
            ])
            ->addCriteria($this->createUserCriteria($user))
            ->addCriteria($this->createBetweenDateCriteria($startDate, $endDate))
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function createBetweenDateCriteria(DateTime $startDate = null, DateTime $endDate = null)
    {
        $criteria = Criteria::create();

        if ($startDate) {
            $criteria->andWhere(Criteria::expr()->gte('t.date', $startDate));
        }

        if ($endDate) {
            $criteria->andWhere(Criteria::expr()->lte('t.date', $startDate));
        }

        return $criteria;
    }

    public function createUserCriteria(User $user)
    {
        return Criteria::create()->andWhere(Criteria::expr()->eq('t.user', $user));
    }
}

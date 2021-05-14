<?php

declare(strict_types=1);

namespace App\Repository\Task;

use App\Entity\Task\TasksReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TasksReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TasksReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TasksReport[]    findAll()
 * @method TasksReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasksReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TasksReport::class);
    }

    // /**
    //  * @return TaskReport[] Returns an array of TaskReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskReport
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

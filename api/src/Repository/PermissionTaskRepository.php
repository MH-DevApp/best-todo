<?php

namespace App\Repository;

use App\Entity\PermissionTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PermissionTask>
 *
 * @method PermissionTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method PermissionTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method PermissionTask[]    findAll()
 * @method PermissionTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PermissionTask::class);
    }

//    /**
//     * @return PermissionTask[] Returns an array of PermissionTask objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PermissionTask
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

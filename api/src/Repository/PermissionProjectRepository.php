<?php

namespace App\Repository;

use App\Entity\PermissionProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PermissionProject>
 *
 * @method PermissionProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method PermissionProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method PermissionProject[]    findAll()
 * @method PermissionProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermissionProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PermissionProject::class);
    }

//    /**
//     * @return PermissionProject[] Returns an array of PermissionProject objects
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

//    public function findOneBySomeField($value): ?PermissionProject
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

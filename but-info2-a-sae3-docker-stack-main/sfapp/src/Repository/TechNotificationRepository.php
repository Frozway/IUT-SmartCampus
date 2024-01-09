<?php

namespace App\Repository;

use App\Entity\TechNotification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TechNotification>
 *
 * @method TechNotification|null find($id, $lockMode = null, $lockVersion = null)
 * @method TechNotification|null findOneBy(array $criteria, array $orderBy = null)
 * @method TechNotification[]    findAll()
 * @method TechNotification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechNotification::class);
    }

//    /**
//     * @return TechNotification[] Returns an array of TechNotification objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TechNotification
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

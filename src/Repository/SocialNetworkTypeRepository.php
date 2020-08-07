<?php

namespace App\Repository;

use App\Entity\SocialNetworkType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SocialNetworkType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocialNetworkType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocialNetworkType[]    findAll()
 * @method SocialNetworkType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocialNetworkTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialNetworkType::class);
    }

    // /**
    //  * @return SocialNetworkType[] Returns an array of SocialNetworkType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SocialNetworkType
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

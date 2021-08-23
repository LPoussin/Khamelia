<?php

namespace App\Repository;

use App\Entity\ConstanteSpecialite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConstanteSpecialite|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstanteSpecialite|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstanteSpecialite[]    findAll()
 * @method ConstanteSpecialite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstanteSpecialiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstanteSpecialite::class);
    }

    // /**
    //  * @return ConstanteSpecialite[] Returns an array of ConstanteSpecialite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ConstanteSpecialite
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\ConstanteJour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ConstanteJour|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstanteJour|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstanteJour[]    findAll()
 * @method ConstanteJour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstanteJourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstanteJour::class);
    }

    // /**
    //  * @return ConstanteJour[] Returns an array of ConstanteJour objects
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
    public function findOneBySomeField($value): ?ConstanteJour
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

<?php

namespace App\Repository;

use App\Entity\LiaisonCentreSante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LiaisonCentreSante|null find($id, $lockMode = null, $lockVersion = null)
 * @method LiaisonCentreSante|null findOneBy(array $criteria, array $orderBy = null)
 * @method LiaisonCentreSante[]    findAll()
 * @method LiaisonCentreSante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiaisonCentreSanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiaisonCentreSante::class);
    }

    // /**
    //  * @return LiaisonCentreSante[] Returns an array of LiaisonCentreSante objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LiaisonCentreSante
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

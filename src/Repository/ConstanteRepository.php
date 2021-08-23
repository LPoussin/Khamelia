<?php

namespace App\Repository;

use App\Entity\Constante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Constante|null find($id, $lockMode = null, $lockVersion = null)
 * @method Constante|null findOneBy(array $criteria, array $orderBy = null)
 * @method Constante[]    findAll()
 * @method Constante[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstanteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Constante::class);
    }

    // /**
    //  * @return Constante[] Returns an array of Constante objects
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
    public function findOneBySomeField($value): ?Constante
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

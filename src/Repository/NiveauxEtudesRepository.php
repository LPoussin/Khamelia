<?php

namespace App\Repository;

use App\Entity\NiveauxEtudes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NiveauxEtudes|null find($id, $lockMode = null, $lockVersion = null)
 * @method NiveauxEtudes|null findOneBy(array $criteria, array $orderBy = null)
 * @method NiveauxEtudes[]    findAll()
 * @method NiveauxEtudes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NiveauxEtudesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NiveauxEtudes::class);
    }

    // /**
    //  * @return NiveauxEtudes[] Returns an array of NiveauxEtudes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NiveauxEtudes
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

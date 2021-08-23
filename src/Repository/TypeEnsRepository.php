<?php

namespace App\Repository;

use App\Entity\TypeEns;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeEns|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeEns|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeEns[]    findAll()
 * @method TypeEns[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeEnsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEns::class);
    }

    // /**
    //  * @return TypeEns[] Returns an array of TypeEns objects
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
    public function findOneBySomeField($value): ?TypeEns
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

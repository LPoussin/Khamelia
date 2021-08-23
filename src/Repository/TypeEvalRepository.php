<?php

namespace App\Repository;

use App\Entity\TypeEval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeEval|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeEval|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeEval[]    findAll()
 * @method TypeEval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeEvalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEval::class);
    }

    // /**
    //  * @return TypeEval[] Returns an array of TypeEval objects
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
    public function findOneBySomeField($value): ?TypeEval
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

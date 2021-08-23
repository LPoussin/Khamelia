<?php

namespace App\Repository;

use App\Entity\CategorieEval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CategorieEval|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieEval|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieEval[]    findAll()
 * @method CategorieEval[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieEvalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieEval::class);
    }

    // /**
    //  * @return CategorieEval[] Returns an array of CategorieEval objects
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
    public function findOneBySomeField($value): ?CategorieEval
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

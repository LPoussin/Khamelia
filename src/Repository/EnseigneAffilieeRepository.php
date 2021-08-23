<?php

namespace App\Repository;

use App\Entity\EnseigneAffiliee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnseigneAffiliee|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnseigneAffiliee|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnseigneAffiliee[]    findAll()
 * @method EnseigneAffiliee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnseigneAffilieeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnseigneAffiliee::class);
    }

    // /**
    //  * @return EnseigneAffiliee[] Returns an array of EnseigneAffiliee objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EnseigneAffiliee
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

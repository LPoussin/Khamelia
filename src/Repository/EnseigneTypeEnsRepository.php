<?php

namespace App\Repository;

use App\Entity\EnseigneTypeEns;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnseigneTypeEns|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnseigneTypeEns|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnseigneTypeEns[]    findAll()
 * @method EnseigneTypeEns[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnseigneTypeEnsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnseigneTypeEns::class);
    }

    // /**
    //  * @return EnseigneTypeEns[] Returns an array of EnseigneTypeEns objects
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
    public function findOneBySomeField($value): ?EnseigneTypeEns
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

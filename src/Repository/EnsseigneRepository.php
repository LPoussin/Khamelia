<?php

namespace App\Repository;

use App\Entity\Ensseigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ensseigne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ensseigne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ensseigne[]    findAll()
 * @method Ensseigne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnsseigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ensseigne::class);
    }

    // /**
    //  * @return Ensseigne[] Returns an array of Ensseigne objects
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
    public function findOneBySomeField($value): ?Ensseigne
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

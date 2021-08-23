<?php

namespace App\Repository;

use App\Entity\ProfesseurMatiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProfesseurMatiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfesseurMatiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfesseurMatiere[]    findAll()
 * @method ProfesseurMatiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfesseurMatiereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfesseurMatiere::class);
    }

    // /**
    //  * @return ProfesseurMatiere[] Returns an array of ProfesseurMatiere objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProfesseurMatiere
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

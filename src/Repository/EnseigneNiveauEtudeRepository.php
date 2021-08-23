<?php

namespace App\Repository;

use App\Entity\EnseigneNiveauEtude;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EnseigneNiveauEtude|null find($id, $lockMode = null, $lockVersion = null)
 * @method EnseigneNiveauEtude|null findOneBy(array $criteria, array $orderBy = null)
 * @method EnseigneNiveauEtude[]    findAll()
 * @method EnseigneNiveauEtude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnseigneNiveauEtudeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EnseigneNiveauEtude::class);
    }

    // /**
    //  * @return EnseigneNiveauEtude[] Returns an array of EnseigneNiveauEtude objects
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
    public function findOneBySomeField($value): ?EnseigneNiveauEtude
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

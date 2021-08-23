<?php

namespace App\Repository;

use App\Entity\DemandeConsultation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DemandeConsultation|null find($id, $lockMode = null, $lockVersion = null)
 * @method DemandeConsultation|null findOneBy(array $criteria, array $orderBy = null)
 * @method DemandeConsultation[]    findAll()
 * @method DemandeConsultation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DemandeConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeConsultation::class);
    }

    // /**
    //  * @return DemandeConsultation[] Returns an array of DemandeConsultation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findByPatientAttenteField($enseigne,$specialite, $is_valided)
    {
        return $this->createQueryBuilder('cs')
            ->andWhere('cs.id_enseigne = ?1')
            ->setParameter(1,$enseigne)
            ->andWhere('cs.is_valided = ?2')
            ->setParameter(2, $is_valided)
            ->andWhere('cs.id_specialite = ?3')
            ->setParameter(3, $specialite)
            ->groupBy('cs.id_patient')
            ->getQuery()
            ->getResult();
    }
    /*
    public function findOneBySomeField($value): ?DemandeConsultation
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

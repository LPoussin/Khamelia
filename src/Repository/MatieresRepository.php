<?php

namespace App\Repository;

use App\Entity\EnseigneMatiere;
use App\Entity\Matieres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Matieres|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matieres|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matieres[]    findAll()
 * @method Matieres[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatieresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matieres::class);
    }

    // /**
    //  * @return Matieres[] Returns an array of Matieres objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function findSpecialite($id_enseigne)
    {
        //$entityManager = $this->getDoctrine()->getManager();

        $qb = $this->_em->createQueryBuilder();
        $qb2 = $qb;
        $qb2->select('ms.id_matiere')
            ->from('\App\Entity\EnseigneMatiere', 'ms')
            ->where('ms.id_enseigne = ?1');

        $qb  = $this->_em->createQueryBuilder();
        $qb->select('mm')
            ->from('\App\Entity\Matieres', 'mm')
            ->where($qb->expr()->notIn('mm.id', $qb2->getDQL()));
        $qb->setParameter(1, $id_enseigne);
        $query  = $qb->getQuery();

        return $query->getResult();

    }
    /*
    public function findOneBySomeField($value): ?Matieres
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

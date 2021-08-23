<?php

namespace App\Repository;

use App\Entity\InvitationMedecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InvitationMedecin|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvitationMedecin|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvitationMedecin[]    findAll()
 * @method InvitationMedecin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationMedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvitationMedecin::class);
    }

    // /**
    //  * @return InvitationMedecin[] Returns an array of InvitationMedecin objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findInvitaion($id_enseigne, $id_medecin)
    {
        //$entityManager = $this->getDoctrine()->getManager();

        $qb = $this->createQueryBuilder('im');

        return  $qb
            ->andWhere('im.id_medecin = :id_med AND im.id_enseigne = :id_ens')
            ->setParameters(array('id_med' => $id_medecin,'id_ens' => $id_enseigne))->getQuery()->getResult();
        
    }
    /*
    public function findOneBySomeField($value): ?InvitationMedecin
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

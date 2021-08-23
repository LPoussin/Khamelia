<?php

namespace App\Repository;

use App\Entity\UserJoinedEnseigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserJoinedEnseigne|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserJoinedEnseigne|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserJoinedEnseigne[]    findAll()
 * @method UserJoinedEnseigne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserJoinedEnseigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserJoinedEnseigne::class);
    }


    public function findByProfilesField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.profiles LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->groupBy('u.id_user')
            ->getQuery()
            ->getResult();
    }

    public function findByProfilesAndEnseigneField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.profiles LIKE :val')
            ->setParameter('val', '%'.$value['profile'].'%')
            ->andWhere('u.id_enseigne = :id_enseigne')
            ->setParameter('id_enseigne', $value['id_enseigne'])
            ->groupBy('u.id_user')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return UserJoinedEnseigne[] Returns an array of UserJoinedEnseigne objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserJoinedEnseigne
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

<?php

namespace App\Repository;

use App\Entity\Bal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\User;

/**
 * @method Bal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bal[]    findAll()
 * @method Bal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bal::class);
    }
    
   public function getAll(User $user){
       $qb=$this->createQueryBuilder('m')
               ->leftJoin('m.destinataires','d')
               ->leftJoin('m.expediteur','exp')
               ->leftJoin('m.destinatairegroupes','dg')
               ->leftJoin('dg.members','mem')
               ->where('exp=:user');
             return $qb->orWhere($qb->expr()->in(':user',':d'))
               ->orWhere($qb->expr()->in(':user',':mm'))
               ->setParameter('user',$user)
                     ->setParameter('d','m.destinataires')
                     ->setParameter('mm','m.destinatairegroupes')
                     ->addSelect('d')
                     ->addSelect('exp')
                     ->addSelect('dg')
                     ->addSelect('mem')
               ->getQuery()
               ->getResult();
   }
}

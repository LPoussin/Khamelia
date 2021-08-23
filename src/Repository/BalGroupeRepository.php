<?php

namespace App\Repository;

use App\Entity\BalGroupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BalGroupe|null find($id, $lockMode = null, $lockVersion = null)
 * @method BalGroupe|null findOneBy(array $criteria, array $orderBy = null)
 * @method BalGroupe[]    findAll()
 * @method BalGroupe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BalGroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BalGroupe::class);
    }

   public function getAllGroupeWithMembers(){
       return $this->createQueryBuilder('g')
               ->leftJoin('g.members','m')
               ->addSelect('m')
               ->getQuery()
               ->getResult();
   }
}

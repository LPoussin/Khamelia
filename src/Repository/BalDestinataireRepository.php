<?php

namespace App\Repository;

use App\Entity\BalDestinataire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\BalGroupe;
use App\Entity\UserJoinedEnseigne;

/**
 * @method BalDestinataire|null find($id, $lockMode = null, $lockVersion = null)
 * @method BalDestinataire|null findOneBy(array $criteria, array $orderBy = null)
 * @method BalDestinataire[]    findAll()
 * @method BalDestinataire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BalDestinataireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BalDestinataire::class);
    }

   public function getBalParts(User $user) {
        return $this->createQueryBuilder('d')
                ->innerJoin('d.message','m')
                ->leftJoin('d.user','user')
                ->innerJoin('m.expediteur','expediteur')
                ->where('m.expediteur = :user')
                ->orWhere('d.user = :user')
                ->setParameter('user',$user)
                ->addSelect('m')
                ->addSelect('expediteur')
                ->addSelect('user')
                //->orderBy('d.id','DESC')
                ->getQuery()
                ->getResult();
    }
    
    public function getBalGroupes($groupe){
         $qb=$this->createQueryBuilder('d')
                ->innerJoin('d.message','m')
                ->leftJoin('d.user','user')
                 ->leftJoin('d.groupe','groupe')
                ->leftJoin('groupe.members','gmembers')
                ->innerJoin('m.expediteur','expediteur')
                ->where('groupe.id=:groupe')
                ->setParameter('user',$user)
                ->setParameter('groupe',$groupe)
                ->getQuery()
                ->getResult();
    }
    
    public function getAll(UserJoinedEnseigne $user){
        $qb=$this->createQueryBuilder('d')
                ->innerJoin('d.message','m')
                ->leftJoin('d.user','user')
                ->leftJoin('d.groupe','groupe')
                ->leftJoin('groupe.members','gmembers')
                ->innerJoin('m.expediteur','expediteur')
                ->where('m.expediteur = :user')
                ->orWhere('d.user = :user');
                return $qb->orWhere($qb->expr()->in(':user',':gmembers'))
                ->setParameter('user',$user)
                ->setParameter('gmembers','gmembers')
                ->addSelect('m')
                ->addSelect('expediteur')
                ->addSelect('user')
                ->addSelect('groupe')
                ->getQuery()
                ->getResult();
    }
    
    public function getMessageUser($paginator,$page,$limit,User $me,User $us){
       $qb=  $this->createQueryBuilder('d')
                ->innerJoin('d.message','m')
                ->leftJoin('d.user','user')
               ->innerJoin('m.expediteur','expediteur')
                ->add('where','expediteur=:me and user=:us or (expediteur=:us and user=:me)')
               ->setParameter('me',$me)
                ->setParameter('us',$us)
               ->addSelect('m')
                ->addSelect('expediteur')
                ->addSelect('user')
               ->orderBy('d.id','DESC')
               ->getQuery()
                ;
       return $paginator->paginate($qb,$page,$limit);
    }
    
    public function getMessageGroupe($paginator,$page,$limit,BalGroupe $gs){
        $qb=$this->createQueryBuilder('d')
                ->innerJoin('d.message','m')
               ->innerJoin('m.expediteur','expediteur')
                 ->addSelect('m')
                ->addSelect('expediteur')
                ->leftJoin('d.groupe','groupe')
                ->leftJoin('groupe.members','gmembers')
                ->where('groupe.id=:gr')
                 ->setParameter('gr',$gs->getId())
                ->orderBy('d.id','DESC')
                ->getQuery()
                ;
        return $paginator->paginate($qb,$page,$limit);
    }
}

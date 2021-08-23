<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserJoinedEnseigne;

/**
 * Description of AppropriateUserForChat
 *
 * @author 
 */
class AppropriateUserForChat {
    private $em;

    function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    /**
     * @return ?UserJoinedEnseigne[]
     */
    public function getUsers(UserJoinedEnseigne $userj){
        //tous les enseignes de l'utilisateur
        $qb = $this->em->getRepository(UserJoinedEnseigne::class)->createQueryBuilder('uj');
        $pps=$qb->innerJoin('uj.enseigne','enseigne')
                ->addSelect('enseigne')
                ->where('uj.id_user=:id_user')
                ->setParameter('id_user',$userj->getIdUser())->getQuery()->getResult();
        $all=[];$all_id=[];
        foreach ($pps as $userjoin){ 
            $alls=$this->getEnseigneMembersJoin($userjoin->getIdEnseigne());
          if(in_array($this->typeUser($userjoin), ['eleve',"admin"])) {
            foreach ($alls as $s){
                if(!in_array($s->getIdUser(), $all_id)){
                    $all[]=$s->getUser();
                    $all_id[]=$s->getIdUser();
                }
            }
          }else if($this->typeUser($userjoin)=="parent_ensei"){
            //sélectionner les parents et eleves
            $myclasses=$userjoin->getUser()->getProfesseurOfClasses();
            foreach ($myclasses as $cl){
              $ins=$cl->getInscriptions();
              foreach ($ins as $i){
                if(!in_array($i->getIdPere(), $all_id)){
                   $all[]=$i->getPere();
                    $all_id[]=$i->getIdPere(); 
                }
                if(!in_array($i->getIdMere(), $all_id)){
                   $all[]=$i->getMere();
                   $all_id[]=$i->getIdMere(); 
                }
                if($i->getIdTuteur() and !in_array($i->getIdTuteur(), $all_id)){
                   $all[]=$i->getTuteur();
                   $all_id[]=$i->getIdTuteur(); 
                }
                if(!in_array($i->getIdEleve()->getId(), $all_id)){
                   $all[]=$i->getIdEleve();
                   $all_id[]=$i->getIdEleve()->getId(); 
                }  
              }
            }
          }else if($this->typeUser($userjoin)=="enseignant"){
              //sélectionner les parents et eleves
            $myclasses=$userjoin->getUser()->getProfesseurOfClasses();
            foreach ($myclasses as $cl){
              $ins=$cl->getInscriptions();
              foreach ($ins as $i){
                if(!in_array($i->getIdPere(), $all_id)){
                   $all[]=$i->getPere();
                    $all_id[]=$i->getIdPere(); 
                }
                if(!in_array($i->getIdMere(), $all_id)){
                   $all[]=$i->getMere();
                   $all_id[]=$i->getIdMere(); 
                }
                if($i->getIdTuteur() and !in_array($i->getIdTuteur(), $all_id)){
                   $all[]=$i->getTuteur();
                   $all_id[]=$i->getIdTuteur(); 
                }
                if(!in_array($i->getIdEleve()->getId(), $all_id)){
                   $all[]=$i->getIdEleve();
                   $all_id[]=$i->getIdEleve()->getId(); 
                }  
              }
            }
          }elseif($this->typeUser($userjoin)=="parent"){ //selectionner les parents des enfants de la classe que ses enfants
              foreach ($alls as $mem){
                 $type_tmp = $this->typeUser($mem);
                 if($mem->getIdUser()!=$userjoin->getIdUser() and $type_tmp==="admin" and !in_array($mem->getIdUser(), $all_id)){ //reste le cas des eleves et parent d'eleve
                    $all[]=$mem->getUser();
                    $all_id[]=$mem->getIdUser();
                 }
                 $suisperedesenfants=$userjoin->getUser()->getPeres();
                 foreach ($suisperedesenfants as $i){
                     $this->serParent($i, $all, $all_id);
                 }
                 $suismeredesenfants=$userjoin->getUser()->getMeres();
                 foreach ($suismeredesenfants as $i){
                     $this->serParent($i, $all, $all_id);
                 }
                 $suistuteurdesenfants=$userjoin->getUser()->getTuteurs();
                 foreach ($suistuteurdesenfants as $i){
                     $this->serParent($i, $all, $all_id);
                 }
              }
              
          }
          
        }
        return $all_id;
    }
    private function serParent(\App\Entity\Inscriptions $i,&$all,&$all_id){
        $classeins=$i->getClasse()->getInscriptions();
         foreach ($classeins as $i2){
             if($i2->getIdEleve()->getId()!=$i->getIdEleve()->getId()){
                if(!in_array($i2->getIdPere(), $all_id)){
                    $all[]=$i2->getPere();
                    $all_id[]=$i2->getIdPere(); 
                }
                if(!in_array($i2->getIdMere(), $all_id)){
                   $all[]=$i2->getMere();
                   $all_id[]=$i2->getIdMere(); 
                }
                if($i2->getIdTuteur() and !in_array($i2->getIdTuteur(), $all_id)){
                   $all[]=$i2->getTuteur();
                   $all_id[]=$i2->getIdTuteur(); 
                }
             }
         }
    }
    private function typeUser(UserJoinedEnseigne $user){
       if(in_array('eleve', $user->getProfiles() )){
            return "eleve";
        }
        if(in_array('parent', $user->getProfiles()) and in_array('enseignant', $user->getProfiles()) ){
            return "parent_ensei";
        }
        
        if(in_array('parent', $user->getProfiles())){
            return "parent";
        }
        if(in_array('enseignant', $user->getProfiles())){
            return "enseignant";
        }
        return empty($user->getProfiles()) ? null : "admin";
    }
    
    public function getEnseigneMembersJoin($enseigne_id){
        $qb = $this->em->getRepository(UserJoinedEnseigne::class)->createQueryBuilder('uj');
        return $qb->innerJoin('uj.user','user')
                ->addSelect('user')
                ->leftJoin('user.inscriptions','ins')
                ->leftJoin('user.peres','meperes')//inscriptions
                ->leftJoin('user.meres','memeres')
                ->leftJoin('user.tuteurs','metut')
                //->leftJoin('meperes.id_eleve','enfant')
                ->leftJoin('user.professeurOfClasses','profC')
                ->leftJoin('profC.inscriptions','classIns')
                ->leftJoin('classIns.id_eleve','eleve')
                ->leftJoin('classIns.pere','pere')
                ->leftJoin('classIns.mere','mere')
                ->leftJoin('classIns.tuteur','tuteur')
                ->leftJoin('ins.classe','cl')
                ->leftJoin('cl.professeurs','prof')
                ->where('uj.id_enseigne=:id')
                ->addSelect('ins')
                ->addSelect('profC')
                ->addSelect('classIns')
                ->addSelect('pere')
                ->addSelect('mere')
                ->addSelect('tuteur')
                ->addSelect('meperes')
                 ->addSelect('memeres')
                 ->addSelect('metut')
                 ->addSelect('eleve')
                ->addSelect('cl')
                ->addSelect('prof')
                ->setParameter('id',$enseigne_id)->getQuery()->getResult();
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EntityListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Cours;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of NewCourseListener
 *
 * @author ril.inetschools
 */
class NewCourseListener {
    private $em;
    
    function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }
    public function postPersist(Cours $cours,LifecycleEventArgs $event){
       // $prof=$cours->getIdProf();
        $classe=$cours->getIdClasse();
        $ins=$classe->getInscriptions();
        $destinataire=[];
        $destIds=[];
        foreach ($ins as $i){
            if(!in_array($i->getIdEleve()->getId(), $destIds)){
               $destinataire[]=$i->getIdEleve(); 
            }
            if(!in_array($i->getIdPere(), $destIds) and $i->getPere()){
               $destinataire[]=$i->getPere(); 
            }
            if(!in_array($i->getIdMere(), $destIds) and $i->getMere()){
               $destinataire[]=$i->getMere(); 
            }
            if(!in_array($i->getIdTuteur(), $destIds) and $i->getTuteur()){
               $destinataire[]=$i->getTuteur(); 
            }
        }
        $notif_type="new_course";
        $notif_content="Un nouveau cours pour ".$classe->getLibelle();
        foreach ($destinataire as $dest){
            $n=new Notification();
            $n->setContenu($notif_content)
                    ->setCreatedAt(new \DateTime)
                    ->setVu(false)
                    ->setDestinataire($dest)
                    ->setType($notif_type);
            $this->em->persist($n);
        }
        $this->em->flush();
    }
}

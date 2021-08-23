<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EntityListener;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\EvaluationNote;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Description of NewCourseListener
 *
 * @author ril.inetschools
 */
class NewNoteListener {
    private $em;
    
    function __construct(EntityManagerInterface $em) {
        $this->em=$em;
    }
    public function postPersist(EvaluationNote $note,LifecycleEventArgs $event){
       // Seront notifier seulement l'eleve et ses parents
        $evaluation=$note->getEvaluation();
        $classe=$evaluation->getClasse();
        $eleve=$note->getEleve();
        $ins= $this->em->getRepository(Inscriptions::class)->findOneBy(['classe'=>$classe,"id_eleve"=>$eleve]);
        $destinataire=[];
        
        if(!in_null($ins->getIdPere())){
           $destinataire[]=$ins->getPere(); 
        }
        if(!in_null($ins->getIdMere()) and $ins->getIdMere()!=$ins->getIdPere()){
           $destinataire[]=$ins->getMere(); 
        }
        $notif_type="new_note";
        $notif_content=$eleve->getNom()." ".$eleve->getPrenom()." noté pour l'évaluation ".$evaluation->getLibelle();
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

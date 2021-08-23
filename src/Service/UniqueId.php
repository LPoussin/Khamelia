<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service;

/**
 * Description of UniqueId
 *
 * @author --ril-inetschools
 */
class UniqueId {
    private $letters=['A','B','C','D','E','F','G','H','I','J','K','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    
    public function AssignUniqueId(string $last_id){
        //Vérifier la donnée
        if(!preg_match('/[1-9][0-9]{3}_[A-Z]{2}-[0-9]{2}/', $last_id)){
           throw new \Exception("INETSCHOOLS unique code Generator : Invalide unique code format. Veuillez rapporter le problème aux administrateurs du site"); 
        }
        
        $x1 = explode('_', $last_id);
        $x2= explode('-',$x1[1]);
        
        $part1=(int)$x1[0];
        $part2=$x2[0];
        $part3=(int)$x2[1];
        
        $num='';
        if($part1<9999){
            $num= $this->buildId($part1+1,$part2,$part3);
        }else{
          //==9999
           $part1=1000; //on reéinitialise $part1
           $first=$part2[0];//premiere lettre de la partie 2
           $seconde=$part2[1];//deuxieme lettre de la partie 2
           $ind2= array_search($seconde, $this->letters);
           $ind1= array_search($first, $this->letters);
           if($ind2 < 24){ 
               $seconde= $this->letters[$ind2+1];
               $part2=$first.$seconde;
               $num= $this->buildId($part1,$part2,$part3);             
           }else{// deuxiemem lettre ==Z
           //si premiere lettre !=Z
               if($ind1 < 24){
                  $first= $this->letters[$ind1+1]; 
                  $part2=$first."A"; //seconde devient la lettre A
                  $num=$this->buildId($part1,$part2,$part3); 
               }else{
                   //premiere lettre Z aussi
                   $part2="AA";
                   if($part3 == 99){
                       // toutes les combinaisons possibles ont été prise
                        throw new \Exception("INETSCHOOLS unique code Generator : busy unique code. Veuillez rapporter le problème aux administrateurs du site");
                   }
                   $part3+=1;
                   $num=$this->buildId($part1,$part2,$part3);   
               }
              
           }
        }
        return $num;
    }
    
    private function buildId(int $part1,string $part2,int $part3){
       if($part3 < 10){
           $part3 = '0'.$part3;
       }
       return "$part1"."_"."$part2-$part3";
    }
}

<?php

namespace App\Entity;

use App\Repository\BalRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\UserJoinedEnseigne;
use App\Entity\BalDestinataire as Dest;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\BalGroupe;
/**
 * @ORM\Entity(repositoryClass=BalRepository::class)
 */
class Bal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
    /**
     *
     * @var ?string
     * @ORM\Column(type="text",nullable=true)
     */
    private $message;
    /**
     *
     * @var ?string
     * @ORM\Column(type="string",length=255)
     */
    private $subject;
    /**
     * @var ?string
     * @ORM\Column(type="string",nullable=true,length=255)
     */
    private $file;
    /**
     * @var ?string
     * @ORM\Column(type="string",nullable=true,length=10)
     */
    private $filetype;
    private $f;
    /**
     *
     * @var ?\DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;
    /**
     * Utiliser Assert 
     * @var ?UserJoinedEnseigne
     * @ORM\ManyToOne(targetEntity=User::class,cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $expediteur;
    
    /*
     * @ORM\ManyToMany(targetEntity=User::class,cascade={"persist"},inversedBy="balreceives") 
     */
    private $destinataires=[];
    /*
     * @ORM\ManyToMany(targetEntity=BalGroupe::class,cascade={"persist"}) 
     */
    private $destinatairegroupes=[];
    /**
     *
     * @ORM\OneToMany(targetEntity=Dest::class,cascade={"persist"},mappedBy="message") 
     */
    private $dests;
    
    function __construct() {
        $this->destinataires=new ArrayCollection();
        $this->destinatairegroupes=new ArrayCollection();
        $this->dests=new ArrayCollection();
    }

    public function getFiletype(){
        return $this->filetype;
    }
    public function setFiletype($type){
        $this->filetype=$type;
    }

    public function getId(){
        return $this->id;
    }
    
    function getMessage(): ?string {
        return $this->message;
    }

    function getFile(): ?string {
        return $this->file;
    }

    function getDate(): ?\DateTime {
        return $this->date;
    }

    function getExpediteur(): ?User {
        return $this->expediteur;
    }

    function setMessage(?string $message){
        $this->message = $message;return $this;
    }

    function setFile(?string $file){
        $this->file = $file;return $this;
    }

    function setDate(?\DateTime $date){
        $this->date = $date;return $this;
    }

    function setExpediteur(?User $expediteur) {
        $this->expediteur = $expediteur;return $this;
    }
    /**
     * @return Collection|User[]
     */
    public function getDestinataires() {
        return $this->destinataires;
    }
    public function addDestinataire(User $dest){
        if(!$this->destinataires->contains($dest)){
            $this->destinataires[]=$dest;
           // $dest->setMessage($this);
        }
        return $this;
    }
    public function removeDestinataire(User $ad){
        if($this->destinataires->contains($ad)){
             $this->destinataires->removeElement($ad);
        }
        return $this;
    }
    
    public function getF() {
        return $this->f;
    }

    public function setF($f) {
        $this->f = $f;
        return $this;
    }
    public function getSubject(): ?string {
        return $this->subject;
    }

    public function setSubject(?string $subject) {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return Collection|BalGroupe[]
     */
    public function getDestinatairegroupes() {
        return $this->destinatairegroupes;
    }
    public function addDestinatairegroupe(BalGroupe $dest){
        if(!$this->destinatairegroupes->contains($dest)){
            $this->destinatairegroupes[]=$dest;
            
        }
        return $this;
    }
    public function removeDestinatairegroupe(BalGroupe $ad){
        if($this->destinatairegroupes->contains($ad)){
           $this->destinatairegroupes->removeElement($ad);
        }
        return $this;
    }
    
    /**
     * @return Collection|Dest[]
     */
    public function getDests() {
        return $this->dests;
    }
    public function addDest(Dest $dest){
        if(!$this->dests->contains($dest)){
            $this->dests[]=$dest;
            $dest->setMessage($this);
        }
        return $this;
    }
    public function removeDest(Dest $ad){
        if($this->dests->contains($ad)){
           $this->dests->removeElement($ad);
        }
        return $this;
    }
}

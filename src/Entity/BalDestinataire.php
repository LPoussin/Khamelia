<?php

namespace App\Entity;

use App\Repository\BalDestinataireRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Bal as Message;
use App\Entity\BalGroupe as Groupe;

/**
 * @ORM\Entity(repositoryClass=BalDestinataireRepository::class)
 */
class BalDestinataire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
    /**
     * @var  ?User
     * @ORM\ManyToOne(targetEntity=User::class,cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true)
     */
    private $user;
    /**
     * @var  ?Groupe
     * @ORM\ManyToOne(targetEntity=Groupe::class,cascade={"persist"})
     */
    private $groupe;
    /**
     * @ORM\ManyToOne(targetEntity=Message::class,cascade={"persist"},inversedBy="dests")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     * @var ?Message 
     */
    private $message;
    
    /**
     * Etat du message : vu ou pas
     * @var ?bool
     * @ORM\Column(type="boolean")
     */
    private $etat;

    public function getId(){
        return $this->id;
    }

    public function getMessage(): ?Message {
        return $this->message;
    }

    public function setMessage(?Message $message) {
        $this->message = $message;
        return $this;
    }
    
    public function getUser(): ?User {
        return $this->user;
    }

    public function getGroupe(): ?Groupe {
        return $this->groupe;
    }

    public function setUser(?User $user) {
        $this->user = $user;
        return $this;
    }

    public function setGroupe(?Groupe $groupe) {
        $this->groupe = $groupe;
        return $this;
    }
    public function getEtat(): ?bool {
        return $this->etat;
    }

    public function setEtat(?bool $etat) {
        $this->etat = $etat;
        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\BalGroupeRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BalGroupeRepository::class)
 */
class BalGroupe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     *
     * @var ?string
     * @ORM\Column(type="string",length=255)
     */
    private $libelle;
    /**
     * @ORM\ManyToMany(targetEntity=User::class,cascade={"persist"})
     * Assert\NotNull()
     */
    private $members;
    
    function __construct() {
        $this->members=new ArrayCollection();
    }

    public function getId(): ?int{
        return $this->id;
    }
    public function getLibelle(): ?string {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle) {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * 
     * @return Collection|User[]
     */
    public function getMembers() {
        return $this->members;
    }
    public function addMember(?User $dest){
        if(!$this->members->contains($dest)){
            $this->members[]=$dest;
        }
        return $this;
    }
    public function removeMember(?User $ad){
        if($this->members->contains($ad)){
             $this->members->removeElement($ad);
        }
        return $this;
    }
}

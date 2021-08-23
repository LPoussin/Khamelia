<?php

namespace App\Entity;

use App\Repository\EnseigneAffilieeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=EnseigneAffilieeRepository::class)
 */
class EnseigneAffiliee
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="enseigneAffiliees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_entreprise;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom_enseigne;

    /**
     * @ORM\Column(type="string", length=75)
     */
    private $code_enseigne;

    /**
     * @ORM\Column(type="date")
     */
    private $date_affiliation;
    /**
     * --ril--
     * @ORM\OneToMany(targetEntity=Classes::class,mappedBy="enseigne")
     */
    private $classes=[];
    
    function __construct() {
        $this->classes=new ArrayCollection();
    }

    public function setId(int $value){
        $this->id = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEntreprise(): ?User
    {
        return $this->id_entreprise;
    }

    public function setIdEntreprise(?User $id_entreprise): self
    {
        $this->id_entreprise = $id_entreprise;

        return $this;
    }

    public function getNomEnseigne(): ?string
    {
        return $this->nom_enseigne;
    }

    public function setNomEnseigne(string $nom_enseigne): self
    {
        $this->nom_enseigne = $nom_enseigne;

        return $this;
    }

    public function getCodeEnseigne(): ?string
    {
        return $this->code_enseigne;
    }

    public function setCodeEnseigne(string $code_enseigne): self
    {
        $this->code_enseigne = $code_enseigne;

        return $this;
    }

    public function getDateAffiliation(): ?\DateTimeInterface
    {
        return $this->date_affiliation;
    }

    public function setDateAffiliation(\DateTimeInterface $date_affiliation): self
    {
        $this->date_affiliation = $date_affiliation;

        return $this;
    }
    /**
     * @return Collection|Classes[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClasse(Classes $evaluation): self
    {
        if (!$this->classes->contains($evaluation)) {
            $this->classes[] = $evaluation;
            $evaluation->setEnseigne($this);
        }
        return $this;
    }

    public function removeClasse(Classes $evaluation): self{
        if ($this->classes->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getEnseigne() === $this) {
                $evaluation->setEnseigne(null);
            }
        }
        return $this;
    }

}

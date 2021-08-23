<?php

namespace App\Entity;

use App\Repository\NiveauxEtudesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NiveauxEtudesRepository::class)
 */
class NiveauxEtudes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=TypeEns::class, inversedBy="niveauxEtudes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeEnseigne;

    /**
     * @ORM\OneToMany(targetEntity=AssocMatiereNiveau::class, mappedBy="niveauEtude")
     */
    private $assocMatiereNiveaux;

    /**
     * @ORM\OneToMany(targetEntity=Classes::class, mappedBy="niveau")
     */
    private $classes;



    public function __construct()
    {
        $this->assocMatiereNiveaux = new ArrayCollection();
        $this->classes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTypeEnseigne(): ?TypeEns
    {
        return $this->typeEnseigne;
    }

    public function setTypeEnseigne(?TypeEns $typeEnseigne): self
    {
        $this->typeEnseigne = $typeEnseigne;

        return $this;
    }

    /**
     * @return Collection|AssocMatiereNiveau[]
     */
    public function getAssocMatiereNiveaux(): Collection
    {
        return $this->assocMatiereNiveaux;
    }

    public function addAssocMatiereNiveau(AssocMatiereNiveau $assocMatiereNiveau): self
    {
        if (!$this->assocMatiereNiveaux->contains($assocMatiereNiveau)) {
            $this->assocMatiereNiveaux[] = $assocMatiereNiveau;
            $assocMatiereNiveau->setNiveauEtude($this);
        }

        return $this;
    }

    public function removeAssocMatiereNiveau(AssocMatiereNiveau $assocMatiereNiveau): self
    {
        if ($this->assocMatiereNiveaux->removeElement($assocMatiereNiveau)) {
            // set the owning side to null (unless already changed)
            if ($assocMatiereNiveau->getNiveauEtude() === $this) {
                $assocMatiereNiveau->setNiveauEtude(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classes $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->setNiveau($this);
        }

        return $this;
    }

    public function removeClass(Classes $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getNiveau() === $this) {
                $class->setNiveau(null);
            }
        }

        return $this;
    }

   
}

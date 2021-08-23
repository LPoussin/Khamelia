<?php

namespace App\Entity;

use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeriesRepository::class)
 */
class Series
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
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=AssocMatiereNiveau::class, mappedBy="serie")
     */
    private $assocMatiereNiveaux;

    /**
     * @ORM\OneToMany(targetEntity=Classes::class, mappedBy="serie")
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $assocMatiereNiveau->setSerie($this);
        }

        return $this;
    }

    public function removeAssocMatiereNiveau(AssocMatiereNiveau $assocMatiereNiveau): self
    {
        if ($this->assocMatiereNiveaux->removeElement($assocMatiereNiveau)) {
            // set the owning side to null (unless already changed)
            if ($assocMatiereNiveau->getSerie() === $this) {
                $assocMatiereNiveau->setSerie(null);
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
            $class->setSerie($this);
        }

        return $this;
    }

    public function removeClass(Classes $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getSerie() === $this) {
                $class->setSerie(null);
            }
        }

        return $this;
    }

   
}

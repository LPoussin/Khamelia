<?php

namespace App\Entity;

use App\Repository\TypeEnsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeEnsRepository::class)
 */
class TypeEns
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=50)
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
     * @ORM\Column(type="boolean")
     */
    private $serie;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity=NiveauxEtudes::class, mappedBy="typeEnseigne")
     */
    private $niveauxEtudes;

    /**
     * @ORM\OneToMany(targetEntity=Ensseigne::class, mappedBy="enseigneType")
     */
    private $ensseignes;

    public function __construct()
    {
        $this->niveauxEtudes = new ArrayCollection();
        $this->ensseignes = new ArrayCollection();
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

    public function getSerie(): ?bool
    {
        return $this->serie;
    }

    public function setSerie(bool $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|NiveauxEtudes[]
     */
    public function getNiveauxEtudes(): Collection
    {
        return $this->niveauxEtudes;
    }

    public function addNiveauxEtude(NiveauxEtudes $niveauxEtude): self
    {
        if (!$this->niveauxEtudes->contains($niveauxEtude)) {
            $this->niveauxEtudes[] = $niveauxEtude;
            $niveauxEtude->setTypeEnseigne($this);
        }

        return $this;
    }

    public function removeNiveauxEtude(NiveauxEtudes $niveauxEtude): self
    {
        if ($this->niveauxEtudes->removeElement($niveauxEtude)) {
            // set the owning side to null (unless already changed)
            if ($niveauxEtude->getTypeEnseigne() === $this) {
                $niveauxEtude->setTypeEnseigne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ensseigne[]
     */
    public function getEnsseignes(): Collection
    {
        return $this->ensseignes;
    }

    public function addEnsseigne(Ensseigne $ensseigne): self
    {
        if (!$this->ensseignes->contains($ensseigne)) {
            $this->ensseignes[] = $ensseigne;
            $ensseigne->setEnseigneType($this);
        }

        return $this;
    }

    public function removeEnsseigne(Ensseigne $ensseigne): self
    {
        if ($this->ensseignes->removeElement($ensseigne)) {
            // set the owning side to null (unless already changed)
            if ($ensseigne->getEnseigneType() === $this) {
                $ensseigne->setEnseigneType(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\CategorieEvalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieEvalRepository::class)
 */
class CategorieEval
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
     * @ORM\OneToMany(targetEntity=TypeEval::class, mappedBy="categorie")
     */
    private $typeEvals;

    public function __construct()
    {
        $this->typeEvals = new ArrayCollection();
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
     * @return Collection|TypeEval[]
     */
    public function getTypeEvals(): Collection
    {
        return $this->typeEvals;
    }

    public function addTypeEval(TypeEval $typeEval): self
    {
        if (!$this->typeEvals->contains($typeEval)) {
            $this->typeEvals[] = $typeEval;
            $typeEval->setCategorie($this);
        }

        return $this;
    }

    public function removeTypeEval(TypeEval $typeEval): self
    {
        if ($this->typeEvals->removeElement($typeEval)) {
            // set the owning side to null (unless already changed)
            if ($typeEval->getCategorie() === $this) {
                $typeEval->setCategorie(null);
            }
        }

        return $this;
    }
}

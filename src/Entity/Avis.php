<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AvisRepository::class)
 */
class Avis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cours_compris;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $bien_explique;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_valide;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="avis")
     */
    private $eleve;

    /**
     * @ORM\ManyToOne(targetEntity=Cours::class, inversedBy="avis")
     */
    private $cours;

    public function __construct()
    {
        $this->is_valide = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoursCompris(): ?string
    {
        return $this->cours_compris;
    }

    public function setCoursCompris(string $cours_compris): self
    {
        $this->cours_compris = $cours_compris;

        return $this;
    }

    public function getBienExplique(): ?string
    {
        return $this->bien_explique;
    }

    public function setBienExplique(string $bien_explique): self
    {
        $this->bien_explique = $bien_explique;

        return $this;
    }

    public function getIsValide(): ?bool
    {
        return $this->is_valide;
    }

    public function setIsValide(bool $is_valide): self
    {
        $this->is_valide = $is_valide;

        return $this;
    }

    public function getEleve(): ?User
    {
        return $this->eleve;
    }

    public function setEleve(?User $eleve): self
    {
        $this->eleve = $eleve;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\InscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionsRepository::class)
 */
class Inscriptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_eleve;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_pere;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_mere;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_tuteur;
    /**
     * @var ?User --ril--
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="peres")
     */
    private $pere;
    /**
     * @var ?User --ril--
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="meres")
     */
    private $mere;
    /**
     * @var ?User --ril--
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tuteurs")
     */
    private $tuteur;

    /**
     * @ORM\ManyToOne(targetEntity=Classes::class, inversedBy="inscriptions")
     */
    private $classe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEleve(): ?User
    {
        return $this->id_eleve;
    }

    public function setIdEleve(?User $id_eleve): self
    {
        $this->id_eleve = $id_eleve;

        return $this;
    }

    public function getIdPere(): ?int
    {
        return $this->id_pere;
    }

    public function setIdPere(int $id_pere): self
    {
        $this->id_pere = $id_pere;

        return $this;
    }

    public function getIdMere(): ?int
    {
        return $this->id_mere;
    }

    public function setIdMere(int $id_mere): self
    {
        $this->id_mere = $id_mere;

        return $this;
    }

    public function getIdTuteur(): ?int
    {
        return $this->id_tuteur;
    }

    public function setIdTuteur(?int $id_tuteur): self
    {
        $this->id_tuteur = $id_tuteur;

        return $this;
    }

    public function getClasse(): ?Classes
    {
        return $this->classe;
    }

    public function setClasse(?Classes $classe): self
    {
        $this->classe = $classe;

        return $this;
    }
    
    public function getPere(): ?User {
        return $this->pere;
    }

    public function getMere(): ?User {
        return $this->mere;
    }

    public function getTuteur(): ?User {
        return $this->tuteur;
    }

    public function setPere(?User $pere) {
        $this->pere = $pere;
        return $this;
    }

    public function setMere(?User $mere) {
        $this->mere = $mere;
        return $this;
    }

    public function setTuteur(?User $tuteur) {
        $this->tuteur = $tuteur;
        return $this;
    }
}

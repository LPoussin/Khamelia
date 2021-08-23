<?php

namespace App\Entity;

use App\Repository\EnseigneMatiereRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnseigneMatiereRepository::class)
 */
class EnseigneMatiere
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_matiere;

    /**
     * @ORM\Column(type="integer")
     */
    private $coeff;

    /**
     * @ORM\Column(type="integer")
     */
    private $horaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdEnseigne(): ?int
    {
        return $this->id_enseigne;
    }

    public function setIdEnseigne(int $id_enseigne): self
    {
        $this->id_enseigne = $id_enseigne;

        return $this;
    }

    public function getIdMatiere(): ?int
    {
        return $this->id_matiere;
    }

    public function setIdMatiere(int $id_matiere): self
    {
        $this->id_matiere = $id_matiere;

        return $this;
    }

    public function getCoeff(): ?int
    {
        return $this->coeff;
    }

    public function setCoeff(int $coeff): self
    {
        $this->coeff = $coeff;

        return $this;
    }

    public function getHoraire(): ?int
    {
        return $this->horaire;
    }

    public function setHoraire(int $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }
}

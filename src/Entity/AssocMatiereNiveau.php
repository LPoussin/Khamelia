<?php

namespace App\Entity;

use App\Repository\AssocMatiereNiveauRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AssocMatiereNiveauRepository::class)
 */
class AssocMatiereNiveau
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=NiveauxEtudes::class, inversedBy="assocMatiereNiveaux")
     */
    private $niveauEtude;

    /**
     * @ORM\ManyToOne(targetEntity=Series::class, inversedBy="assocMatiereNiveaux")
     */
    private $serie;

    /**
     * @ORM\ManyToOne(targetEntity=Matieres::class, inversedBy="assocMatiereNiveaux")
     */
    private $matiere;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $coef;

    /**
     * @ORM\Column(type="integer")
     */
    private $horaire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNiveauEtude(): ?NiveauxEtudes
    {
        return $this->niveauEtude;
    }

    public function setNiveauEtude(?NiveauxEtudes $niveauEtude): self
    {
        $this->niveauEtude = $niveauEtude;

        return $this;
    }

    public function getSerie(): ?Series
    {
        return $this->serie;
    }

    public function setSerie(?Series $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getMatiere(): ?Matieres
    {
        return $this->matiere;
    }

    public function setMatiere(?Matieres $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
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

    public function getCoef(): ?int
    {
        return $this->coef;
    }

    public function setCoef(int $coef): self
    {
        $this->coef = $coef;

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

<?php

namespace App\Entity;

use App\Repository\DemandeConsultationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemandeConsultationRepository::class)
 */
class DemandeConsultation
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
    private $id_secretaire;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_patient;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_specialite;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_valided;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSecretaire(): ?int
    {
        return $this->id_secretaire;
    }

    public function setIdSecretaire(int $id_secretaire): self
    {
        $this->id_secretaire = $id_secretaire;

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

    public function getIdPatient(): ?int
    {
        return $this->id_patient;
    }

    public function setIdPatient(int $id_patient): self
    {
        $this->id_patient = $id_patient;

        return $this;
    }

    public function getIdSpecialite(): ?int
    {
        return $this->id_specialite;
    }

    public function setIdSpecialite(int $id_specialite): self
    {
        $this->id_specialite = $id_specialite;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getIsValided(): ?bool
    {
        return $this->is_valided;
    }

    public function setIsValided(bool $is_valided): self
    {
        $this->is_valided = $is_valided;

        return $this;
    }
}

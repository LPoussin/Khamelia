<?php

namespace App\Entity;

use App\Repository\ConstanteJourRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConstanteJourRepository::class)
 */
class ConstanteJour
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
    private $id_constante;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_infirmier;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_specialite;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_patient;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle_cst;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdConstante(): ?int
    {
        return $this->id_constante;
    }

    public function setIdConstante(int $id_constante): self
    {
        $this->id_constante = $id_constante;

        return $this;
    }

    public function getIdInfirmier(): ?int
    {
        return $this->id_infirmier;
    }

    public function setIdInfirmier(int $id_infirmier): self
    {
        $this->id_infirmier = $id_infirmier;

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

    public function getIdPatient(): ?int
    {
        return $this->id_patient;
    }

    public function setIdPatient(int $id_patient): self
    {
        $this->id_patient = $id_patient;

        return $this;
    }

    public function getLibelleCst(): ?string
    {
        return $this->libelle_cst;
    }

    public function setLibelleCst(string $libelle_cst): self
    {
        $this->libelle_cst = $libelle_cst;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

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
}

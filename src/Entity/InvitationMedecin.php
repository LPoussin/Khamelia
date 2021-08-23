<?php

namespace App\Entity;

use App\Repository\InvitationMedecinRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvitationMedecinRepository::class)
 */
class InvitationMedecin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_checked;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_medecin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsChecked(): ?bool
    {
        return $this->is_checked;
    }

    public function setIsChecked(bool $is_checked): self
    {
        $this->is_checked = $is_checked;

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

    public function getIdMedecin(): ?int
    {
        return $this->id_medecin;
    }

    public function setIdMedecin(int $id_medecin): self
    {
        $this->id_medecin = $id_medecin;

        return $this;
    }
}

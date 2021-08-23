<?php

namespace App\Entity;

use App\Repository\EnseigneNiveauEtudeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnseigneNiveauEtudeRepository::class)
 */
class EnseigneNiveauEtude
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
     * @ORM\Column(type="array")
     */
    private $niveaux = [];

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

    public function getNiveaux(): ?array
    {
        return $this->niveaux;
    }

    public function setNiveaux(array $niveaux): self
    {
        $this->niveaux = $niveaux;

        return $this;
    }
}

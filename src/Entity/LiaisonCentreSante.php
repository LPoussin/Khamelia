<?php

namespace App\Entity;

use App\Repository\LiaisonCentreSanteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LiaisonCentreSanteRepository::class)
 */
class LiaisonCentreSante
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
    private $id_centre_de_sante;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne_affilie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCentreDeSante(): ?int
    {
        return $this->id_centre_de_sante;
    }

    public function setIdCentreDeSante(int $id_centre_de_sante): self
    {
        $this->id_centre_de_sante = $id_centre_de_sante;

        return $this;
    }

    public function getIdEnseigneAffilie(): ?int
    {
        return $this->id_enseigne_affilie;
    }

    public function setIdEnseigneAffilie(int $id_enseigne_affilie): self
    {
        $this->id_enseigne_affilie = $id_enseigne_affilie;

        return $this;
    }
}

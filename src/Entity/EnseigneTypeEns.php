<?php

namespace App\Entity;

use App\Repository\EnseigneTypeEnsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnseigneTypeEnsRepository::class)
 */
class EnseigneTypeEns
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
    private $id_typeEns;

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

    public function getIdTypeEns(): ?int
    {
        return $this->id_typeEns;
    }

    public function setIdTypeEns(int $id_typeEns): self
    {
        $this->id_typeEns = $id_typeEns;

        return $this;
    }
}

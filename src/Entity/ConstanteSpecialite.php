<?php

namespace App\Entity;

use App\Repository\ConstanteSpecialiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConstanteSpecialiteRepository::class)
 */
class ConstanteSpecialite
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
    private $id_specialite;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;


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

    public function getIdSpecialite(): ?int
    {
        return $this->id_specialite;
    }

    public function setIdSpecialite(int $id_specialite): self
    {
        $this->id_specialite = $id_specialite;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}

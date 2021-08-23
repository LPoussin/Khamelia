<?php

namespace App\Entity;

use App\Repository\ProfesseurMatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfesseurMatiereRepository::class)
 */
class ProfesseurMatiere
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="professeurMatiere", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_prof;

    /**
     * @ORM\ManyToMany(targetEntity=Matieres::class, inversedBy="professeurMatieres",cascade={"persist", "remove"})
     */
    private $matieres;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    public function __construct()
    {
        $this->matieres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProf(): ?User
    {
        return $this->id_prof;
    }

    public function setIdProf(User $id_prof): self
    {
        $this->id_prof = $id_prof;

        return $this;
    }

    /**
     * @return Collection|Matieres[]
     */
    public function getMatieres(): Collection
    {
        return $this->matieres;
    }

    public function addMatiere(Matieres $matiere): self
    {
        if (!$this->matieres->contains($matiere)) {
            $this->matieres[] = $matiere;
        }

        return $this;
    }

    public function removeMatiere(Matieres $matiere): self
    {
        $this->matieres->removeElement($matiere);

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

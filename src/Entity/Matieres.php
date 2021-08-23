<?php

namespace App\Entity;

use App\Repository\MatieresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatieresRepository::class)
 */
class Matieres
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity=AssocMatiereNiveau::class, mappedBy="matiere")
     */
    private $assocMatiereNiveaux;

    /**
     * @ORM\ManyToMany(targetEntity=ProfesseurMatiere::class, mappedBy="matieres")
     */
    private $professeurMatieres;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_matiere")
     */
    private $cours;

    /**
     * @ORM\ManyToMany(targetEntity=Classes::class, mappedBy="matieres")
     */
    private $classes;

    /**
     * @ORM\OneToMany(targetEntity=Evaluation::class, mappedBy="matiere")
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity=Medecin::class, mappedBy="matiere")
     */
    private $medecins;


  
    public function __construct()
    {
        $this->assocMatiereNiveaux = new ArrayCollection();
        $this->professeurMatieres = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->medecins = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|AssocMatiereNiveau[]
     */
    public function getAssocMatiereNiveaux(): Collection
    {
        return $this->assocMatiereNiveaux;
    }

    public function addAssocMatiereNiveau(AssocMatiereNiveau $assocMatiereNiveau): self
    {
        if (!$this->assocMatiereNiveaux->contains($assocMatiereNiveau)) {
            $this->assocMatiereNiveaux[] = $assocMatiereNiveau;
            $assocMatiereNiveau->setMatiere($this);
        }

        return $this;
    }

    public function removeAssocMatiereNiveau(AssocMatiereNiveau $assocMatiereNiveau): self
    {
        if ($this->assocMatiereNiveaux->removeElement($assocMatiereNiveau)) {
            // set the owning side to null (unless already changed)
            if ($assocMatiereNiveau->getMatiere() === $this) {
                $assocMatiereNiveau->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProfesseurMatiere[]
     */
    public function getProfesseurMatieres(): Collection
    {
        return $this->professeurMatieres;
    }

    public function addProfesseurMatiere(ProfesseurMatiere $professeurMatiere): self
    {
        if (!$this->professeurMatieres->contains($professeurMatiere)) {
            $this->professeurMatieres[] = $professeurMatiere;
            $professeurMatiere->addMatiere($this);
        }

        return $this;
    }

    public function removeProfesseurMatiere(ProfesseurMatiere $professeurMatiere): self
    {
        if ($this->professeurMatieres->removeElement($professeurMatiere)) {
            $professeurMatiere->removeMatiere($this);
        }

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->setIdMatiere($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getIdMatiere() === $this) {
                $cour->setIdMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    public function addClass(Classes $class): self
    {
        if (!$this->classes->contains($class)) {
            $this->classes[] = $class;
            $class->addMatiere($this);
        }

        return $this;
    }

    public function removeClass(Classes $class): self
    {
        if ($this->classes->removeElement($class)) {
            $class->removeMatiere($this);
        }

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setMatiere($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getMatiere() === $this) {
                $evaluation->setMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Medecin[]
     */
    public function getMedecins(): Collection
    {
        return $this->medecins;
    }

    public function addMedecin(Medecin $medecin): self
    {
        if (!$this->medecins->contains($medecin)) {
            $this->medecins[] = $medecin;
            $medecin->setMatiere($this);
        }

        return $this;
    }

    public function removeMedecin(Medecin $medecin): self
    {
        if ($this->medecins->removeElement($medecin)) {
            // set the owning side to null (unless already changed)
            if ($medecin->getMatiere() === $this) {
                $medecin->setMatiere(null);
            }
        }

        return $this;
    }


    
}

<?php

namespace App\Entity;

use App\Repository\ClassesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClassesRepository::class)
 */
class Classes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=NiveauxEtudes::class, inversedBy="classes")
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity=Series::class, inversedBy="classes")
     */
    private $serie;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="classes")
     */
    private $professeur_principale;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;
    /**
    * --ril--
    * @var ?EnseigneAffiliee 
    * @ORM\ManyToOne(targetEntity=EnseigneAffiliee::class,inversedBy="classes")
    */
    private $enseigne;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="classe")
     */
    private $inscriptions;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_classe")
     */
    private $cours;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="professeurOfClasses")
     */
    private $professeurs;

    /**
     * @ORM\ManyToMany(targetEntity=Matieres::class, inversedBy="classes")
     */
    private $matieres;

    /**
     * @ORM\OneToMany(targetEntity=Evaluation::class, mappedBy="classe")
     */
    private $evaluations;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->professeurs = new ArrayCollection();
        $this->matieres = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    

    public function getNiveau(): ?NiveauxEtudes
    {
        return $this->niveau;
    }

    public function setNiveau(?NiveauxEtudes $niveau): self
    {
        $this->niveau = $niveau;

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

    public function getProfesseurPrincipale(): ?User
    {
        return $this->professeur_principale;
    }

    public function setProfesseurPrincipale(?User $professeur_principale): self
    {
        $this->professeur_principale = $professeur_principale;

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

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Inscriptions[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscriptions $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setClasse($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getClasse() === $this) {
                $inscription->setClasse(null);
            }
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
            $cour->setIdClasse($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getIdClasse() === $this) {
                $cour->setIdClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getProfesseurs(): Collection
    {
        return $this->professeurs;
    }

    public function addProfesseur(User $professeur): self
    {
        if (!$this->professeurs->contains($professeur)) {
            $this->professeurs[] = $professeur;
        }

        return $this;
    }

    public function removeProfesseur(User $professeur): self
    {
        $this->professeurs->removeElement($professeur);

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
            $evaluation->setClasse($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getClasse() === $this) {
                $evaluation->setClasse(null);
            }
        }

        return $this;
    }
    
    public function getEnseigne(): ?EnseigneAffiliee {
        return $this->enseigne;
    }

    public function setEnseigne(?EnseigneAffiliee $enseigne) {
        $this->enseigne = $enseigne;
        return $this;
    }
}

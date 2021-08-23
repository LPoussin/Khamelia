<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoursRepository::class)
 * @ORM\EntityListeners({"App\EntityListener\NewCourseListener"})
 */
class Cours
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Classes::class, inversedBy="cours")
     * @ORM\JoinColumn(nullable=false)
     */
    private $id_classe;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cours")
     */
    private $id_prof;

    /**
     * @ORM\ManyToOne(targetEntity=Matieres::class, inversedBy="cours")
     */
    private $id_matiere;

    /**
     * @ORM\Column(type="date")
     */
    private $date_cours;

    /**
     * @ORM\Column(type="time")
     */
    private $datetime_debut;

    /**
     * @ORM\Column(type="time")
     */
    private $datetime_fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $dure_cours;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $note_public;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $note_privee;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $note_parent;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $note_eleve;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $note_prof;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $note_ecole;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $document;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="cours")
     */
    private $avis;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_enseigne;

    public function __construct()
    {
        $this->actif = true;
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClasse(): ?Classes
    {
        return $this->id_classe;
    }

    public function setIdClasse(?Classes $id_classe): self
    {
        $this->id_classe = $id_classe;

        return $this;
    }

    public function getIdProf(): ?User
    {
        return $this->id_prof;
    }

    public function setIdProf(?User $id_prof): self
    {
        $this->id_prof = $id_prof;

        return $this;
    }

    public function getIdMatiere(): ?Matieres
    {
        return $this->id_matiere;
    }

    public function setIdMatiere(?Matieres $id_matiere): self
    {
        $this->id_matiere = $id_matiere;

        return $this;
    }

    public function getDateCours(): ?\DateTimeInterface
    {
        return $this->date_cours;
    }

    public function setDateCours(\DateTimeInterface $date_cours): self
    {
        $this->date_cours = $date_cours;

        return $this;
    }

    public function getDatetimeDebut(): ?\DateTimeInterface
    {
        return $this->datetime_debut;
    }

    public function setDatetimeDebut(\DateTimeInterface $datetime_debut): self
    {
        $this->datetime_debut = $datetime_debut;

        return $this;
    }

    public function getDatetimeFin(): ?\DateTimeInterface
    {
        return $this->datetime_fin;
    }

    public function setDatetimeFin(\DateTimeInterface $datetime_fin): self
    {
        $this->datetime_fin = $datetime_fin;

        return $this;
    }

    public function getDureCours(): ?string
    {
        return $this->dure_cours;
    }

    public function setDureCours(string $dure_cours): self
    {
        $this->dure_cours = $dure_cours;

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

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNotePublic(): ?float
    {
        return $this->note_public;
    }

    public function setNotePublic(float $note_public): self
    {
        $this->note_public = $note_public;

        return $this;
    }

    public function getNotePrivee(): ?float
    {
        return $this->note_privee;
    }

    public function setNotePrivee(float $note_privee): self
    {
        $this->note_privee = $note_privee;

        return $this;
    }

    public function getNoteParent(): ?float
    {
        return $this->note_parent;
    }

    public function setNoteParent(float $note_parent): self
    {
        $this->note_parent = $note_parent;

        return $this;
    }

    public function getNoteEleve(): ?float
    {
        return $this->note_eleve;
    }

    public function setNoteEleve(float $note_eleve): self
    {
        $this->note_eleve = $note_eleve;

        return $this;
    }

    public function getNoteProf(): ?float
    {
        return $this->note_prof;
    }

    public function setNoteProf(float $note_prof): self
    {
        $this->note_prof = $note_prof;

        return $this;
    }

    public function getNoteEcole(): ?float
    {
        return $this->note_ecole;
    }

    public function setNoteEcole(float $note_ecole): self
    {
        $this->note_ecole = $note_ecole;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(string $document): self
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @return Collection|Avis[]
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avi): self
    {
        if (!$this->avis->contains($avi)) {
            $this->avis[] = $avi;
            $avi->setCours($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getCours() === $this) {
                $avi->setCours(null);
            }
        }

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

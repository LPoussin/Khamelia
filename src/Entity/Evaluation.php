<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvaluationRepository::class)
 * @ORM\EntityListeners({"App\EntityListener\NewEvaluationListener"})
 */
class Evaluation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Classes::class, inversedBy="evaluations")
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="evaluations")
     */
    private $prof;

    /**
     * @ORM\ManyToOne(targetEntity=Matieres::class, inversedBy="evaluations")
     */
    private $matiere;

    /**
     * @ORM\ManyToOne(targetEntity=TypeEval::class, inversedBy="evaluations")
     */
    private $typeEval;

    /**
     * @ORM\Column(type="date")
     */
    private $dateCompo;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActif;

    /**
     * @ORM\Column(type="boolean",  nullable=true)
     */
    private $isCorrigee;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateCorrigee;

    /**
     * @ORM\Column(type="boolean",  nullable=true)
     */
    private $isRendue;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateRendue;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $calculee;

    /**
     * @ORM\Column(type="date",  nullable=true)
     */
    private $dateCalculee;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $notePublique;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $notePrivee;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $noteParents;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $noteEleve;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $noteProf;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $noteEcole;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $corrigeType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClasse(): ?Classes
    {
        return $this->classe;
    }

    public function setClasse(?Classes $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getProf(): ?User
    {
        return $this->prof;
    }

    public function setProf(?User $prof): self
    {
        $this->prof = $prof;

        return $this;
    }

    public function getMatiere(): ?Matieres
    {
        return $this->matiere;
    }

    public function setMatiere(?Matieres $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getTypeEval(): ?TypeEval
    {
        return $this->typeEval;
    }

    public function setTypeEval(?TypeEval $typeEval): self
    {
        $this->typeEval = $typeEval;

        return $this;
    }

    public function getDateCompo(): ?\DateTimeInterface
    {
        return $this->dateCompo;
    }

    public function setDateCompo(\DateTimeInterface $dateCompo): self
    {
        $this->dateCompo = $dateCompo;

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

    public function getIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    public function getIsCorrigee(): ?bool
    {
        return $this->isCorrigee;
    }

    public function setIsCorrigee(bool $isCorrigee): self
    {
        $this->isCorrigee = $isCorrigee;

        return $this;
    }

    public function getDateCorrigee(): ?\DateTimeInterface
    {
        return $this->dateCorrigee;
    }

    public function setDateCorrigee(?\DateTimeInterface $dateCorrigee): self
    {
        $this->dateCorrigee = $dateCorrigee;

        return $this;
    }

    public function getIsRendue(): ?bool
    {
        return $this->isRendue;
    }

    public function setIsRendue(bool $isRendue): self
    {
        $this->isRendue = $isRendue;

        return $this;
    }

    public function getDateRendue(): ?\DateTimeInterface
    {
        return $this->dateRendue;
    }

    public function setDateRendue(?\DateTimeInterface $dateRendue): self
    {
        $this->dateRendue = $dateRendue;

        return $this;
    }

    public function getCalculee(): ?bool
    {
        return $this->calculee;
    }

    public function setCalculee(?bool $calculee): self
    {
        $this->calculee = $calculee;

        return $this;
    }

    public function getDateCalculee(): ?\DateTimeInterface
    {
        return $this->dateCalculee;
    }

    public function setDateCalculee(\DateTimeInterface $dateCalculee): self
    {
        $this->dateCalculee = $dateCalculee;

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

    public function getNotePublique(): ?string
    {
        return $this->notePublique;
    }

    public function setNotePublique(?string $notePublique): self
    {
        $this->notePublique = $notePublique;

        return $this;
    }

    public function getNotePrivee(): ?string
    {
        return $this->notePrivee;
    }

    public function setNotePrivee(?string $notePrivee): self
    {
        $this->notePrivee = $notePrivee;

        return $this;
    }

    public function getNoteParents(): ?\DateTimeInterface
    {
        return $this->noteParents;
    }

    public function setNoteParents(?\DateTimeInterface $noteParents): self
    {
        $this->noteParents = $noteParents;

        return $this;
    }

    public function getNoteEleve(): ?string
    {
        return $this->noteEleve;
    }

    public function setNoteEleve(?string $noteEleve): self
    {
        $this->noteEleve = $noteEleve;

        return $this;
    }

    public function getNoteProf(): ?string
    {
        return $this->noteProf;
    }

    public function setNoteProf(?string $noteProf): self
    {
        $this->noteProf = $noteProf;

        return $this;
    }

    public function getNoteEcole(): ?string
    {
        return $this->noteEcole;
    }

    public function setNoteEcole(string $noteEcole): self
    {
        $this->noteEcole = $noteEcole;

        return $this;
    }

    public function getCorrigeType(): ?string
    {
        return $this->corrigeType;
    }

    public function setCorrigeType(?string $corrigeType): self
    {
        $this->corrigeType = $corrigeType;

        return $this;
    }
}

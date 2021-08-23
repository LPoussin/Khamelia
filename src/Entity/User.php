<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * 
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Services::class, inversedBy="users")
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=Classes::class, mappedBy="professeur_principale")
     */
    private $classes;
    /**
     * @ORM\OneToMany(targetEntity=UserJoinedEnseigne::class, mappedBy="user")
     */
    private $userJoinedEnseignes;

    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="id_eleve")
     */
    private $inscriptions;

    /**
     * @ORM\OneToOne(targetEntity=ProfesseurMatiere::class, mappedBy="id_prof", cascade={"persist", "remove"})
     */
    private $professeurMatiere;

    /**
     * @ORM\OneToMany(targetEntity=Cours::class, mappedBy="id_prof")
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity=Avis::class, mappedBy="eleve")
     */
    private $avis;

    /**
     * @ORM\OneToMany(targetEntity=EnseigneAffiliee::class, mappedBy="id_entreprise")
     */
    private $enseigneAffiliees;

    /**
     * @ORM\ManyToMany(targetEntity=Classes::class, mappedBy="professeurs")
     */
    private $professeurOfClasses;

    /**
     * @ORM\OneToMany(targetEntity=Evaluation::class, mappedBy="prof")
     */
    private $evaluations;
    /**
     * Pere des enfnts
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="pere")
     */
    private $peres;
    /**
     * Mere des enfants inscrits...
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="mere")
     */
    private $meres;
    /**
     * tuteurs des enfants inscrits...
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="tuteur")
     */
    private $tuteurs;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->classes = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->avis = new ArrayCollection();
        $this->enseigneAffiliees = new ArrayCollection();
        $this->professeurOfClasses = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->peres = new ArrayCollection();
        $this->meres = new ArrayCollection();
        $this->tuteurs = new ArrayCollection();
        $this->userJoinedEnseignes=new ArrayCollection();
    }

    public function setId(int $id):self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Services[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Services $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
        }

        return $this;
    }

    public function removeService(Services $service): self
    {
        $this->services->removeElement($service);

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
            $class->setProfesseurPrincipale($this);
        }

        return $this;
    }

    public function removeClass(Classes $class): self
    {
        if ($this->classes->removeElement($class)) {
            // set the owning side to null (unless already changed)
            if ($class->getProfesseurPrincipale() === $this) {
                $class->setProfesseurPrincipale(null);
            }
        }

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
            $inscription->setIdEleve($this);
        }

        return $this;
    }

    public function removeInscription(Inscriptions $inscription): self
    {
        if ($this->inscriptions->removeElement($inscription)) {
            // set the owning side to null (unless already changed)
            if ($inscription->getIdEleve() === $this) {
                $inscription->setIdEleve(null);
            }
        }

        return $this;
    }

    public function getProfesseurMatiere(): ?ProfesseurMatiere
    {
        return $this->professeurMatiere;
    }

    public function setProfesseurMatiere(ProfesseurMatiere $professeurMatiere): self
    {
        $this->professeurMatiere = $professeurMatiere;

        // set the owning side of the relation if necessary
        if ($professeurMatiere->getIdProf() !== $this) {
            $professeurMatiere->setIdProf($this);
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
            $cour->setIdProf($this);
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            // set the owning side to null (unless already changed)
            if ($cour->getIdProf() === $this) {
                $cour->setIdProf(null);
            }
        }

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
            $avi->setEleve($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avi): self
    {
        if ($this->avis->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getEleve() === $this) {
                $avi->setEleve(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EnseigneAffiliee[]
     */
    public function getEnseigneAffiliees(): Collection
    {
        return $this->enseigneAffiliees;
    }

    public function addEnseigneAffiliee(EnseigneAffiliee $enseigneAffiliee): self
    {
        if (!$this->enseigneAffiliees->contains($enseigneAffiliee)) {
            $this->enseigneAffiliees[] = $enseigneAffiliee;
            $enseigneAffiliee->setIdEntreprise($this);
        }

        return $this;
    }

    public function removeEnseigneAffiliee(EnseigneAffiliee $enseigneAffiliee): self
    {
        if ($this->enseigneAffiliees->removeElement($enseigneAffiliee)) {
            // set the owning side to null (unless already changed)
            if ($enseigneAffiliee->getIdEntreprise() === $this) {
                $enseigneAffiliee->setIdEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Classes[]
     */
    public function getProfesseurOfClasses(): Collection
    {
        return $this->professeurOfClasses;
    }

    public function addProfesseurOfClasse(Classes $professeurOfClasse): self
    {
        if (!$this->professeurOfClasses->contains($professeurOfClasse)) {
            $this->professeurOfClasses[] = $professeurOfClasse;
            $professeurOfClasse->addProfesseur($this);
        }

        return $this;
    }

    public function removeProfesseurOfClasse(Classes $professeurOfClasse): self
    {
        if ($this->professeurOfClasses->removeElement($professeurOfClasse)) {
            $professeurOfClasse->removeProfesseur($this);
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
            $evaluation->setProf($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): self
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getProf() === $this) {
                $evaluation->setProf(null);
            }
        }

        return $this;
    }
    
    /**
     * @return Collection|Inscriptions[]
     */
    public function getPeres(): Collection
    {
        return $this->peres;
    }

    public function addPere(Inscriptions $evaluation): self
    {
        if (!$this->peres->contains($evaluation)) {
            $this->peres[] = $evaluation;
            $evaluation->setPere($this);
        }

        return $this;
    }

    public function removePere(Inscriptions $evaluation): self
    {
        if ($this->peres->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getPere() == $this) {
                $evaluation->setPere(null);
            }
        }
        return $this;
    }
    
    /**
     * @return Collection|Inscriptions[]
     */
    public function getMeres(): Collection
    {
        return $this->meres;
    }

    public function addMere(Inscriptions $evaluation): self
    {
        if (!$this->meres->contains($evaluation)) {
            $this->meres[] = $evaluation;
            $evaluation->setMere($this);
        }

        return $this;
    }

    public function removeMere(Inscriptions $evaluation): self
    {
        if ($this->peres->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getMere() == $this) {
                $evaluation->setMere(null);
            }
        }
        return $this;
    }
    /**
     * @return Collection|Inscriptions[]
     */
    public function getTuteurs(): Collection
    {
        return $this->tuteurs;
    }

    public function addTuteur(Inscriptions $evaluation): self
    {
        if (!$this->tuteurs->contains($evaluation)) {
            $this->tuteurs[] = $evaluation;
            $evaluation->setTuteur($this);
        }

        return $this;
    }

    public function removeTuteur(Inscriptions $evaluation): self
    {
        if ($this->tuteurs->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getTuteur() == $this) {
                $evaluation->setTuteur(null);
            }
        }
        return $this;
    }
    //
    /**
     * @return Collection|UserJoinedEnseigne[]
     */
    public function getUserJoinedEnseignes(): Collection
    {
        return $this->userJoinedEnseignes;
    }

    public function addUserJoinedEnseigne(UserJoinedEnseigne $evaluation): self
    {
        if (!$this->userJoinedEnseignes->contains($evaluation)) {
            $this->userJoinedEnseignes[] = $evaluation;
            $evaluation->setUser($this);
        }

        return $this;
    }

    public function removeUserJoinedEnseigne(UserJoinedEnseigne $evaluation): self
    {
        if ($this->userJoinedEnseignes->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getTuteur() == $this) {
                $evaluation->setTuteur(null);
            }
        }
        return $this;
    }
}

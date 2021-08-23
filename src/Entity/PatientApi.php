<?php

namespace App\Entity;

use App\Repository\PatientApiRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * @ORM\Entity(repositoryClass=PatientApiRepository::class)
 * @UniqueEntity(
 *  fields={"email"},
 *  message="L'émail que vous avez indiquez est déja utilisée !"
 * )
 */
class PatientApi implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenoms;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $telephone;

    /**
     * @ORM\Column(type="integer")
     */
    private $compte;

    /**
     * @ORM\Column(type="string")
     *  @Assert\NotNull(message="Vous devez choisir un genre")
     */
    private $genre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractères")
     */
    private $mdp;

    /**
     * @Assert\EqualTo(propertyPath="mdp", message="Vous n'avez pas tapé le même mot de passe ")
     */
    public $confirm_mdp;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_particulier;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_patient;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getCompte(): ?int
    {
        return $this->compte;
    }

    public function setCompte(int $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function eraseCredentials()
    {
        
    }
    public function getSalt()
    {
        
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->mdp;

    }

    public function getIdParticulier(): ?int
    {
        return $this->id_particulier;
    }

    public function setIdParticulier(int $id_particulier): self
    {
        $this->id_particulier = $id_particulier;

        return $this;
    }

    public function getIsPatient(): ?bool
    {
        return $this->is_patient;
    }

    public function setIsPatient(bool $is_patient): self
    {
        $this->is_patient = $is_patient;

        return $this;
    }
}

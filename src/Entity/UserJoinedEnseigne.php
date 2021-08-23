<?php

namespace App\Entity;

use App\Repository\UserJoinedEnseigneRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\EnseigneAffiliee as Enseigne;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass=UserJoinedEnseigneRepository::class)
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="unique_enseigne_user_id",columns={"user_id","enseigne_id","identification_number"})})
 */
class UserJoinedEnseigne
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
     * @var ?Enseigne
     * @ORM\ManyToOne(targetEntity=Enseigne::class,cascade={"persist"})
     */
    private $enseigne;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_user;
    /**
     * @var ?User
     * @ORM\ManyToOne(targetEntity=User::class,cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $profiles = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $droits = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $joined_at;
    /**
     * @var ?string
     * @ORM\Column(type="string",length=13)
     */
    private $identificationNumber;

    public function __construct()
    {
        $this->joined_at = new \DateTime('now');
    }

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

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getProfiles(): ?array
    {
        return $this->profiles;
    }

    public function setProfiles(?array $profiles): self
    {
        $this->profiles = $profiles;

        return $this;
    }

    public function getDroits(): ?array
    {
        return $this->droits;
    }

    public function setDroits(?array $droits): self
    {
        $this->droits = $droits;

        return $this;
    }

    public function getJoinedAt(): ?\DateTimeInterface
    {
        return $this->joined_at;
    }

    public function setJoinedAt(\DateTimeInterface $joined_at): self
    {
        $this->joined_at = $joined_at;

        return $this;
    }
    
    public function getEnseigne(): ?Enseigne {
        return $this->enseigne;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setEnseigne(?Enseigne $enseigne) {
        $this->enseigne = $enseigne;
        return $this;
    }

    public function setUser(?User $user) {
        $this->user = $user;
        return $this;
    }
    
    public function getIdentificationNumber(): ?string {
        return $this->identificationNumber;
    }

    public function setIdentificationNumber(?string $identificationNumber) {
        $this->identificationNumber = $identificationNumber;
        return $this;
    }
}

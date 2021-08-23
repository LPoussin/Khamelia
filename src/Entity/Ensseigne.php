<?php

namespace App\Entity;

use App\Repository\EnsseigneRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EnsseigneRepository::class)
 */
class Ensseigne
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
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 8, max = 20, minMessage = "Verifier la longueur du nuemro de telephone", maxMessage = "max_lenght")
     * @Assert\Regex(pattern="/^[0-9]*$/", message="Rien que des nombre") 
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Assert\Url(
     *    message = "L'addresse '{{ value }}' n'est pas une addresse valide !",
     * )
     */
    private $urlsite;

    /**
     * @ORM\ManyToOne(targetEntity=TypeEns::class, inversedBy="ensseignes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $enseigneType;


    /**
     * @ORM\ManyToOne(targetEntity=Quartier::class, inversedBy="ensseignes")
     * @Assert\NotBlank()
     * @ORM\JoinColumn(nullable=false)
     */
    private $quartier;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $liaison;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getUrlsite(): ?string
    {
        return $this->urlsite;
    }

    public function setUrlsite(string $urlsite): self
    {
        $this->urlsite = $urlsite;

        return $this;
    }

    public function getEnseigneType(): ?TypeEns
    {
        return $this->enseigneType;
    }

    public function setEnseigneType(?TypeEns $enseigneType): self
    {
        $this->enseigneType = $enseigneType;

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getLiaison(): ?bool
    {
        return $this->liaison;
    }

    public function setLiaison(?bool $liaison): self
    {
        $this->liaison = $liaison;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $iso;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $nicename;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $iso3;

    /**
     * @ORM\Column(type="integer")
     */
    private $numcode;

    /**
     * @ORM\Column(type="integer")
     */
    private $phonecode;

    /**
     * @ORM\OneToMany(targetEntity=Departement::class, mappedBy="country", orphanRemoval=true)
     */
    private $departements;

    public function __construct()
    {
        $this->departements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIso(): ?string
    {
        return $this->iso;
    }

    public function setIso(string $iso): self
    {
        $this->iso = $iso;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNicename(): ?string
    {
        return $this->nicename;
    }

    public function setNicename(string $nicename): self
    {
        $this->nicename = $nicename;

        return $this;
    }

    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    public function setIso3(string $iso3): self
    {
        $this->iso3 = $iso3;

        return $this;
    }

    public function getNumcode(): ?int
    {
        return $this->numcode;
    }

    public function setNumcode(int $numcode): self
    {
        $this->numcode = $numcode;

        return $this;
    }

    public function getPhonecode(): ?int
    {
        return $this->phonecode;
    }

    public function setPhonecode(int $phonecode): self
    {
        $this->phonecode = $phonecode;

        return $this;
    }

    /**
     * @return Collection|Departement[]
     */
    public function getDepartements(): Collection
    {
        return $this->departements;
    }

    public function addDepartement(Departement $departement): self
    {
        if (!$this->departements->contains($departement)) {
            $this->departements[] = $departement;
            $departement->setCountry($this);
        }

        return $this;
    }

    public function removeDepartement(Departement $departement): self
    {
        if ($this->departements->removeElement($departement)) {
            // set the owning side to null (unless already changed)
            if ($departement->getCountry() === $this) {
                $departement->setCountry(null);
            }
        }

        return $this;
    }
}

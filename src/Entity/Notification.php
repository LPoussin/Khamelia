<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
    /**
     *
     * @var ?bool
     * @ORM\Column(type="boolean")
     */
    private $vu;
    /**
     *
     * @var ?User
     * @ORM\ManyToOne(targetEntity=User::class,cascade={"persist"})
     */
    private $destinataire;
    /**
     *
     * @var ?string
     * @ORM\Column(type="string",length=255)
     */
    private $contenu;
    /**
     *
     * @var ?string
     * @ORM\Column(type="string",length=20)
     */
    private $type;
    /**
     *
     * @var ?\DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getId(){
        return $this->id;
    }
    public function getVu(): ?bool {
        return $this->vu;
    }

    public function getDestinataire(): ?User {
        return $this->destinataire;
    }

    public function getContenu(): ?string {
        return $this->contenu;
    }

    public function getCreatedAt(): ?\DateTime {
        return $this->createdAt;
    }

    public function setVu(?bool $vu) {
        $this->vu = $vu;
        return $this;
    }

    public function setDestinataire(?User $destinataire) {
        $this->destinataire = $destinataire;
        return $this;
    }

    public function setContenu(?string $contenu) {
        $this->contenu = $contenu;
        return $this;
    }

    public function setCreatedAt(?\DateTime $createdAt) {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getType(): ?string {
        return $this->type;
    }

    public function setType(?string $type) {
        $this->type = $type;
        return $this;
    }


}

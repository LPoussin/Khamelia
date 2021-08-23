<?php

namespace App\Entity;

use App\Repository\EvaluationNoteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvaluationNoteRepository::class)
 * @ORM\EntityListeners({"App\EntityListener\NewNoteListener"})
 */
class EvaluationNote
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;
    /**
    * @ORM\ManyToOne(targetEntity=Evaluation::class,inversedBy="notes",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $evaluation;
    /**
    * @ORM\ManyToOne(targetEntity=User::class,inversedBy="notes",cascade={"persist"})
    * @ORM\JoinColumn(nullable=false)
    */
    private $eleve;
    /**
     * @var ?string
     * @ORM\Column(type="float")
     */
    private $note;

    public function getId(){
        return $this->id;
    }
    
    public function getEvaluation(): ?Evaluation {
        return $this->evaluation;
    }

    public function getEleve(): ?User {
        return $this->eleve;
    }

    public function getNote(): ?string {
        return $this->note;
    }

    public function setEvaluation(?Evaluation $evaluation) {
        $this->evaluation = $evaluation;
        return $this;
    }

    public function setEleve(?User $eleve) {
        $this->eleve = $eleve;
        return $this;
    }

    public function setNote(?string $note) {
        $this->note = $note;
        return $this;
    }
}

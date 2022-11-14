<?php

namespace App\Entity;

use App\Repository\InstructionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups; 
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InstructionRepository::class)]
class Instruction
{
    #[Groups(["getAllInstructions","getInstruction"])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Phrase associée à une instruction
    // Ex : presser la menthe
    #[ORM\Column(length: 255)]
    #[Groups(["getAllInstructions","getInstruction"])]
    private ?string $phrase = null;

    // Statut de l'instruction
    // Ex : on
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Une instruction doit avoir un statut")]
    #[Assert\NotNull()]
    #[Assert\Choice(choices: ['on','off'], message: "Veuillez entrer un statut")]
    private ?string $status = null;

    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'instructionRecette')]
    #[Groups(["getAllInstructions","getInstruction"])]
    private Collection $recettes;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }
    /**
     * Obtenir l'id d'une instruction
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Obtenir la phrase (instruction)
     *
     * @return string|null
     */
    public function getPhrase(): ?string
    {
        return $this->phrase;
    }
    /**
     * Donner une phrase (instruction)
     *
     * @param string $phrase
     * @return self
     */
    public function setPhrase(string $phrase): self
    {
        $this->phrase = $phrase;

        return $this;
    }
    /**
     * Obtenir le status d'une instruction
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    /**
     * Donner un status à une instruction
     *
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
    /**
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }
    /**
     * Ajouter une recette à une instruction
     *
     * @param Recette $recette
     * @return self
     */
    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->addInstructionRecette($this);
        }

        return $this;
    }
    /**
     * Supprimer une recette liée à une instruction
     *
     * @param Recette $recette
     * @return self
     */
    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->removeElement($recette)) {
            $recette->removeInstructionRecette($this);
        }

        return $this;
    }
}

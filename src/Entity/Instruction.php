<?php

namespace App\Entity;

use App\Repository\InstructionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstructionRepository::class)]
class Instruction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Phrase associée à une instruction
    // Ex : presser la menthe
    #[ORM\Column(length: 255)]
    private ?string $phrase = null;
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
}

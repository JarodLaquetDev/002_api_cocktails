<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllIngredients","getIngredient","createRecette","getRecette"])]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Un ingredient doit avoir un nom")]
    #[Assert\Length(min: 3, minMessage: "Le nom de l'ingredient doit etre superieur a {{ limit }} caractere")]
    #[Assert\NotNull()]
    #[ORM\Column(length: 20)]
    #[Groups(["getAllIngredients","getRecette", "getAllRecettes","getIngredient","createRecette"])]
    private ?string $ingredientName = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Un ingredient doit avoir une quantite")]
    #[Assert\Length(min: 1, minMessage: "Le nom de l'ingredient doit etre superieur a {{ limit }} caractere")]
    #[Assert\NotNull()]
    #[Groups(["getAllIngredients","getRecette", "getAllRecettes","getIngredient"])]
    private ?float $ingredientQuantity = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Un ingredient doit avoir un statut")]
    #[Assert\NotNull()]
    #[Assert\Choice(choices: ['on','off'], message: "Veuillez entrer un statut")]
    private ?string $status = null;

    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'recetteIngredients')]
    #[Groups(["getIngredient","test"])]
    private Collection $ingredientRecette;

    public function __construct()
    {
        $this->ingredientRecette = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredientName(): ?string
    {
        return $this->ingredientName;
    }

    public function setIngredientName(string $ingredientName): self
    {
        $this->ingredientName = $ingredientName;

        return $this;
    }

    public function getIngredientQuantity(): ?float
    {
        return $this->ingredientQuantity;
    }

    public function setIngredientQuantity(float $ingredientQuantity): self
    {
        $this->ingredientQuantity = $ingredientQuantity;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getIngredientRecette(): Collection
    {
        return $this->ingredientRecette;
    }

    public function addIngredientRecette(Recette $ingredientRecette): self
    {
        if (!$this->ingredientRecette->contains($ingredientRecette)) {
            $this->ingredientRecette->add($ingredientRecette);
            $ingredientRecette->addRecetteIngredient($this);
        }

        return $this;
    }

    public function removeIngredientRecette(Recette $ingredientRecette): self
    {
        if ($this->ingredientRecette->removeElement($ingredientRecette)) {
            $ingredientRecette->removeRecetteIngredient($this);
        }

        return $this;
    }
}

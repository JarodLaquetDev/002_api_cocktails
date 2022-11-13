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
    // Identifiant unique
    // Ex : 130
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getAllIngredients","getIngredient","createRecette","getRecette"])]
    private ?int $id = null;

    // Nom d'un ingrédient
    // Ex : pomme 
    #[Assert\NotBlank(message: "Un ingredient doit avoir un nom")]
    #[Assert\Length(min: 3, minMessage: "Le nom de l'ingredient doit etre superieur a {{ limit }} caractere")]
    #[Assert\NotNull()]
    #[ORM\Column(length: 20)]
    #[Groups(["getAllIngredients","getRecette", "getAllRecettes","getIngredient","createRecette"])]
    private ?string $ingredientName = null;

    // Quantité de l'ingrédient
    // Ex : 2
    #[ORM\Column]
    #[Assert\NotBlank(message: "Un ingredient doit avoir une quantite")]
    #[Assert\Length(min: 1, minMessage: "Le nom de l'ingredient doit etre superieur a {{ limit }} caractere")]
    #[Assert\NotNull()]
    #[Groups(["getAllIngredients","getRecette", "getAllRecettes","getIngredient"])]
    private ?float $ingredientQuantity = null;

    // Statut de l'ingrédient
    // Ex : on
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Un ingredient doit avoir un statut")]
    #[Assert\NotNull()]
    #[Assert\Choice(choices: ['on','off'], message: "Veuillez entrer un statut")]
    private ?string $status = null;

    #[ORM\ManyToMany(targetEntity: Recette::class, mappedBy: 'recetteIngredients')]
    #[Groups(["getAllIngredients","getRecette", "getAllRecettes","getIngredient"])]
    private Collection $ingredientRecette;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["getAllIngredients","getRecette", "getAllRecettes","getIngredient"])]
    private ?Picture $ingredientImage = null;

    public function __construct()
    {
        $this->ingredientRecette = new ArrayCollection();
    }


    /**
     * Obtenir l'id d'un ingrédient
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Obtenir le nom d'un ingrédient
     *
     * @return string|null
     */
    public function getIngredientName(): ?string
    {
        return $this->ingredientName;
    }
    /**
     * Donner un nom à un ingrédient
     *
     * @param string $ingredientName
     * @return self
     */
    public function setIngredientName(string $ingredientName): self
    {
        $this->ingredientName = $ingredientName;

        return $this;
    }
    /**
     * Obtenir la quantité d'un ingrédient
     *
     * @return float|null
     */
    public function getIngredientQuantity(): ?float
    {
        return $this->ingredientQuantity;
    }
    /**
     * Donner une quantité à un ingrédient
     *
     * @param float $ingredientQuantity
     * @return self
     */
    public function setIngredientQuantity(float $ingredientQuantity): self
    {
        $this->ingredientQuantity = $ingredientQuantity;

        return $this;
    }
    /**
     * Obtenir le status d'un ingrédient
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    /**
     * Donner un status à un ingrédien
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
     * Obtenir la liste des recettes associées à un ingrédient
     *
     * @return Collection
     */
    public function getIngredientRecette(): Collection
    {
        return $this->ingredientRecette;
    }
    /**
     * Ajouter une recette associée à un ingrédient
     *
     * @param Recette $ingredientRecette
     * @return self
     */
    public function addIngredientRecette(Recette $ingredientRecette): self
    {
        if (!$this->ingredientRecette->contains($ingredientRecette)) {
            $this->ingredientRecette->add($ingredientRecette);
            $ingredientRecette->addRecetteIngredient($this);
        }

        return $this;
    }
    /**
     * Supprimer une recette associée à un ingrédient
     *
     * @param Recette $ingredientRecette
     * @return self
     */
    public function removeIngredientRecette(Recette $ingredientRecette): self
    {
        if ($this->ingredientRecette->removeElement($ingredientRecette)) {
            $ingredientRecette->removeRecetteIngredient($this);
        }

        return $this;
    }
    /**
     * Obtenir l'image d'un ingrédient
     *
     * @return Picture|null
     */
    public function getIngredientImage(): ?Picture
    {
        return $this->ingredientImage;
    }
    /**
     * Donner une image à un ingrédient
     *
     * @param Picture|null $ingredientImage
     * @return self
     */
    public function setIngredientImage(?Picture $ingredientImage): self
    {
        $this->ingredientImage = $ingredientImage;

        return $this;
    }
}

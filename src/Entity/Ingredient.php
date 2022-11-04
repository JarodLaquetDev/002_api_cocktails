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
    #[Groups(["getIngredient","test"])]
    private Collection $ingredientRecette;

    public function __construct()
    {
        $this->ingredientRecette = new ArrayCollection();
    }

    // Cas d'utilisation : obtenir l'id d'un ingrédient
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : int
    public function getId(): ?int
    {
        return $this->id;
    }
    // Cas d'utilisation : obtenir le nom d'un ingrédient
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getIngredientName(): ?string
    {
        return $this->ingredientName;
    }
    // Cas d'utilisation : donner un nom à un ingrédient
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setIngredientName(string $ingredientName): self
    {
        $this->ingredientName = $ingredientName;

        return $this;
    }
    // Cas d'utilisation : obtenir la quantité d'un ingrédient
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : float
    public function getIngredientQuantity(): ?float
    {
        return $this->ingredientQuantity;
    }
    // Cas d'utilisation : donner une quantité à un ingrédient
    // Paramètre(s) d'entrée : float
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setIngredientQuantity(float $ingredientQuantity): self
    {
        $this->ingredientQuantity = $ingredientQuantity;

        return $this;
    }
    // Cas d'utilisation : obtenir le status d'un ingrédient
    // Paramètre(s) d'entrée : 
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getStatus(): ?string
    {
        return $this->status;
    }
    // Cas d'utilisation : donner un status à un ingrédient
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    // Cas d'utilisation : obtenir la liste des recettes associées à un ingrédient
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : Collection
    public function getIngredientRecette(): Collection
    {
        return $this->ingredientRecette;
    }
    // Cas d'utilisation : ajouter une recette associée à un ingrédient
    // Paramètre(s) d'entrée : Recette
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function addIngredientRecette(Recette $ingredientRecette): self
    {
        if (!$this->ingredientRecette->contains($ingredientRecette)) {
            $this->ingredientRecette->add($ingredientRecette);
            $ingredientRecette->addRecetteIngredient($this);
        }

        return $this;
    }
    // Cas d'utilisation : supprimer une recette associée à un ingrédient
    // Paramètre(s) d'entrée : Recette
    // Paramètre(s) de sortie :
    // Valeur de retour : objet
    public function removeIngredientRecette(Recette $ingredientRecette): self
    {
        if ($this->ingredientRecette->removeElement($ingredientRecette)) {
            $ingredientRecette->removeRecetteIngredient($this);
        }

        return $this;
    }
}

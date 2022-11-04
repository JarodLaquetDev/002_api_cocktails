<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    // Identifiant unique
    // Ex : 130
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette","test"])]
    private ?int $id = null;

    // Nom d'une recette
    // Ex : mojito
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Une recette doit avoir un nom")]
    #[Assert\Length(min: 3, minMessage: "Le nom de la recette doit etre superieur a {{ limit }} caractere")]
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette","test"])]
    private ?string $recetteName = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'ingredientRecette')]
    #[Groups(["getRecette","createRecette"])]
    private Collection $recetteIngredients;

    // Status d'une recette
    // Ex : on
    #[ORM\Column(length: 20)]
    private ?string $status = null;

    public function __construct()
    {
        $this->recetteIngredients = new ArrayCollection();
    }
    // Cas d'utilisation : obtenir l'id d'une recette
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : int
    public function getId(): ?int
    {
        return $this->id;
    }
    // Cas d'utilisation : obtenir le nom d'une recette
    // Paramètre(s) d'entrée :
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getRecetteName(): ?string
    {
        return $this->recetteName;
    }
    // Cas d'utilisation : donner un nom à une recette
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : void
    public function setRecetteName(string $recetteName): self
    {
        $this->recetteName = $recetteName;

        return $this;
    }
    // Cas d'utilisation : obtenir les ingrédients associés à une recette
    // Paramètre(s) d'entrée : Collection
    // Paramètre(s) de sortie :
    // Valeur de retour : void
    public function getRecetteIngredients(): Collection
    {
        return $this->recetteIngredients;
    }
    // Cas d'utilisation : ajouter un ingrédient à une recette
    // Paramètre(s) d'entrée : Ingredient
    // Paramètre(s) de sortie :
    // Valeur de retour : void
    public function addRecetteIngredient(Ingredient $recetteIngredient): self
    {
        if (!$this->recetteIngredients->contains($recetteIngredient)) {
            $this->recetteIngredients->add($recetteIngredient);
        }

        return $this;
    }
    // Cas d'utilisation : supprimer un ingrédient d'une recette
    // Paramètre(s) d'entrée : Ingredient
    // Paramètre(s) de sortie :
    // Valeur de retour : void
    public function removeRecetteIngredient(Ingredient $recetteIngredient): self
    {
        $this->recetteIngredients->removeElement($recetteIngredient);

        return $this;
    }
    // Cas d'utilisation : obtenir le status d'une recette
    // Paramètre(s) d'entrée : 
    // Paramètre(s) de sortie :
    // Valeur de retour : string
    public function getStatus(): ?string
    {
        return $this->status;
    }
    // Cas d'utilisation : donner un status à une recette
    // Paramètre(s) d'entrée : string
    // Paramètre(s) de sortie :
    // Valeur de retour : void
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}

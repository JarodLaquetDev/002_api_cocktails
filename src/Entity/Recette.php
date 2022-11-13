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
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette"])]
    private ?int $id = null;

    // Nom d'une recette
    // Ex : mojito
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Une recette doit avoir un nom")]
    #[Assert\Length(min: 3, minMessage: "Le nom de la recette doit etre superieur a {{ limit }} caractere")]
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette","test"])]
    private ?string $recetteName = null;

    // Liste des ingrédients associés à la recette
    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'ingredientRecette')]
    #[Groups(["getRecette","createRecette"])]
    private Collection $recetteIngredients;

    // Status d'une recette
    // Ex : on
    #[ORM\Column(length: 20)]
    private ?string $status = null;

    // Image associée à une recette
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette"])]
    private ?Picture $imageRecette = null;

    public function __construct()
    {
        $this->recetteIngredients = new ArrayCollection();
    }
    /**
     * Obtenir l'id d'une recette
     *
     * @return integer|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Obtenir le nom d'une recette
     *
     * @return string|null
     */
    public function getRecetteName(): ?string
    {
        return $this->recetteName;
    }
    /**
     * Donner un nom à une recette
     *
     * @param string $recetteName
     * @return self
     */
    public function setRecetteName(string $recetteName): self
    {
        $this->recetteName = $recetteName;

        return $this;
    }
    /**
     * Obtenur les ingrédients associés à une recette
     *
     * @return Collection
     */
    public function getRecetteIngredients(): Collection
    {
        return $this->recetteIngredients;
    }
    /**
     * Ajouter un ingrédient à une recette
     *
     * @param Ingredient $recetteIngredient
     * @return self
     */
    public function addRecetteIngredient(Ingredient $recetteIngredient): self
    {
        if (!$this->recetteIngredients->contains($recetteIngredient)) {
            $this->recetteIngredients->add($recetteIngredient);
        }

        return $this;
    }
    /**
     * Supprimer un ingrédient d'une recette
     *
     * @param Ingredient $recetteIngredient
     * @return self
     */
    public function removeRecetteIngredient(Ingredient $recetteIngredient): self
    {
        $this->recetteIngredients->removeElement($recetteIngredient);

        return $this;
    }
    /**
     * Obtenir le status d'une recette
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    /**
     * Donner un status à une recette
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
     * Retourner l'image associée à la recette
     *
     * @return Picture|null
     */
    public function getImageRecette(): ?Picture
    {
        return $this->imageRecette;
    }
    /**
     * Donner une image à une recette
     *
     * @param Picture|null $imageRecette
     * @return self
     */
    public function setImageRecette(?Picture $imageRecette): self
    {
        $this->imageRecette = $imageRecette;

        return $this;
    }
}

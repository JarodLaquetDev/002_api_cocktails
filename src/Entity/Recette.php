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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette","test"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Une recette doit avoir un nom")]
    #[Assert\Length(min: 3, minMessage: "Le nom de la recette doit etre superieur a {{ limit }} caractere")]
    #[Groups(["getRecette", "getAllRecettes","getIngredient","createRecette","test"])]
    private ?string $recetteName = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class, inversedBy: 'ingredientRecette')]
    #[Groups(["getRecette","createRecette"])]
    private Collection $recetteIngredients;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    public function __construct()
    {
        $this->recetteIngredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecetteName(): ?string
    {
        return $this->recetteName;
    }

    public function setRecetteName(string $recetteName): self
    {
        $this->recetteName = $recetteName;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getRecetteIngredients(): Collection
    {
        return $this->recetteIngredients;
    }

    public function addRecetteIngredient(Ingredient $recetteIngredient): self
    {
        if (!$this->recetteIngredients->contains($recetteIngredient)) {
            $this->recetteIngredients->add($recetteIngredient);
        }

        return $this;
    }

    public function removeRecetteIngredient(Ingredient $recetteIngredient): self
    {
        $this->recetteIngredients->removeElement($recetteIngredient);

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
}

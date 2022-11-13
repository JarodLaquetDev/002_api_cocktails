<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Entity\User;
use App\Entity\Instruction;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager): void
    {

        $userNumber = 10;
        $ingredientNumber = 100;
        $recetteNumber = 15;
        $instructionNumber = 30;

        // Création d'un itilisateur Administrateur
        $adminUser = new User();
        $password = "password";
        $adminUser->setUsername('admin')
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword($password)
        ->setStatus("on");
        $manager->persist($adminUser);

        // Création d'utilisateurs
        for ($i=0; $i < $userNumber; $i++) { 
            $userUser = new User();
            $password = $this->faker->password(2,6);
            $userUser->setUsername($this->faker->userName().'@'.$password)
            ->setRoles(['ROLE_USER'])
            ->setPassword($password)
            ->setStatus("on");
            $manager->persist($userUser);
        }

        // Création d'ingrédients
        $listeIngredient = [];
        for($i=0;$i<$ingredientNumber;$i++)
        {
            $ingredient = new Ingredient();
            $ingredient->setIngredientName($this->faker->word());
            $ingredient->setIngredientQuantity($this->faker->randomDigit());
            $ingredient->setStatus("on");
            $listeIngredient[] = $ingredient;
            $manager->persist($ingredient);
        }

                // Création d'instruction :
        for($i=0;$i<$instructionNumber;$i++)
        {
            $instruction = new Instruction();
            $instruction->setPhrase($this->faker->word());
            $instruction->setStatus("on");
            $manager->persist($instruction);
            $listeInstruction[] = $instruction;
        }

        // Création de recettes
        for($i=0;$i<$recetteNumber;$i++)
        {
            $recette = new Recette();
            $recette->setRecetteName($this->faker->word());
            $recette->setStatus("on");
            $recette->addRecetteIngredient($listeIngredient[array_rand($listeIngredient)]);
            $recette->addInstructionRecette($listeInstruction[array_rand($listeInstruction)]);
            $manager->persist($recette);
        }
        $manager->flush();
    }
}

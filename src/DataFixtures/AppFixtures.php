<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Entity\User;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create("fr_FR");
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        $userNumber = 10;
        $ingredientNumber = 100;
        $recetteNumber = 15;

        // Création d'un itilisateur Administrateur
        $adminUser = new User();
        $password = "password";
        $adminUser->setUsername('admin')
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword($this->userPasswordHasher->hashPassword($adminUser, $password))
        ->setStatus("on");
        $manager->persist($adminUser);

        // Création d'utilisateurs
        for ($i=0; $i < $userNumber; $i++) { 
            $userUser = new User();
            $password = $this->faker->password(2,6);
            $userUser->setUsername($this->faker->userName().'@'.$password)
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password))
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

        // Création de recettes
        for($i=0;$i<$recetteNumber;$i++)
        {
            $recette = new Recette();
            $recette->setRecetteName($this->faker->word());
            $recette->setStatus("on");
            $recette->addRecetteIngredient($listeIngredient[array_rand($listeIngredient)]);
            $manager->persist($recette);
        }
        $manager->flush();
    }
}

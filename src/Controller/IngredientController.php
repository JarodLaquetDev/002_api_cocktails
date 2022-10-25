<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/IngredientController.php',
        ]);
    }

    #[Route('/api/ingredients', name: 'ingredients.getAll', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'T\'as pas les droits sale QUEUE')]
    public function getAllIngredient(
        IngredientRepository $repository,
        SerializerInterface $serializer,
        Request $request 
    ) : JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 50);
        $limit = $limit > 20 ? 20: $limit;
        $status = $request->get('status' , 'on');


        $ingredient_name = $request->get('ingredient_name');

        //dd($status);
        //dd(["page" => $page, "limite" => $limit]);

        $ingredient = $repository->findAll();
        //$ingredient = $repository->findWithPagination($page, $limit); //meme chose que $repository->findAll()
        //$ingredient = $repository->findStatusOn($status); //meme chose que $repository->findAll()
        //$ingredient = $repository->findRecetteByIngredient($ingredient_name);

        $jsonIngredients = $serializer->serialize($ingredient, 'json', ['groups' => "getAllIngredients"]);
        return new JsonResponse($jsonIngredients, 200, [], true);
    }
    /*
    #[Route('/api/ingredients/{idIngredient}', name: 'ingredient.get', methods: ['GET'])]
    public function getIngredient(
        int $idIngredient,
        IngredientRepository $repository,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $ingredient = $repository->find($idIngredient);
        $jsonIngredients = $serializer->serialize($ingredient, 'json');
        return $ingredient ? new JsonResponse($jsonIngredients, Response::HTTP_OK, [], true):
        new JsonResponse($jsonIngredients, Response::HTTP_NOT_FOUND, [], false);
    }*/

    #[Route('/api/ingredients/{idIngredient}', name: 'ingredient.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    #[IsGranted('ROLE_USER', message: 'T\'as pas les droits sale QUEUE')]
    #[ParamConverter("ingredient", options: ["id" => "idIngredient"])]
    public function getIngredient(
        Ingredient $ingredient,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $jsonIngredients = $serializer->serialize($ingredient, 'json', ['groups' => "getIngredient"]);
        //$jsonIngredients = $serializer->serialize($ingredient, 'json');
        return new JsonResponse($jsonIngredients, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/ingredients/{idIngredient}', name: 'ingredient.delete', methods: ['DELETE'])]
    #[ParamConverter("ingredient", options: ["id" => "idIngredient"])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    public function deleteIngredient(
        Ingredient $ingredient,
        EntityManagerInterface $entityManager 
    ) : JsonResponse
    {
        $entityManager->remove($ingredient);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/ingredients', name: 'ingredient.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    public function createIngredient(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        RecetteRepository $recetteRepository,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ) : JsonResponse
    {
        $ingredient = $serializer->deserialize($request->getContent(), Ingredient::class, 'json');
        $ingredient->setStatus('on');

        $content = $request->toArray();
        $idRecette = $content['idRecette'];
        $recipe = $recetteRepository->find($idRecette);
        $ingredient->addIngredientRecette($recipe);        

        $errors = $validator->validate($ingredient);
        //dd($errors->count());
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonIngredient = $serializer->serialize($ingredient, "json", ["groups" => 'getIngredient']);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/ingredients/{id}', name: 'ingredient.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    public function updateIngredient(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        RecetteRepository $recetteRepository,
        UrlGeneratorInterface $urlGenerator
    ) : JsonResponse
    {
        $ingredient = $serializer->deserialize(
            $request->getContent(), 
            Ingredient::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $ingredient]
        );
        //$ingredient->setStatus('on');

        $content = $request->toArray();
        $idRecette = $content['idRecette'];

        $ingredient->addIngredientRecette($recetteRepository->find($idRecette));        

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonIngredient = $serializer->serialize($ingredient, "json", ["groups" => 'getIngredient']);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}

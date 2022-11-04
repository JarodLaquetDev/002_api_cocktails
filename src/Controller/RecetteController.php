<?php

namespace App\Controller;

use App\Entity\Recette;
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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'app_recette')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RecetteController.php',
        ]);
    }

    #[Route('/api/recettes', name: 'recette.getAll')]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    #[IsGranted('ROLE_USER', message: 'T\'as pas les droits sale QUEUE')]
    /**
     * Obtenir la liste de toutes les recettes de la BDD
     *
     * @param RecetteRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getAllRecettes(
        RecetteRepository $repository,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $recettes = $repository->findAll();
        $jsonRecettes = $serializer->serialize($recettes, 'json', ['groups' => "getAllRecettes"]);
        return new JsonResponse($jsonRecettes, 200, [], true);
    }

    #[Route('/api/recette/{idRecette}', name: 'recette.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    #[IsGranted('ROLE_USER', message: 'T\'as pas les droits sale QUEUE')]
    #[ParamConverter("recette", options: ["id" => "idRecette"])]
    /**
     * Obtenir les informations d'une recette spécifique de la BDD
     *
     * @param Recette $recette
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getRecette(
        Recette $recette,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $jsonRecette = $serializer->serialize($recette, 'json', ['groups' => "getRecette"]);
        return new JsonResponse($jsonRecette, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/recette/{idRecette}', name: 'recette.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    #[ParamConverter("recette", options: ["id" => "idRecette"])]
    /**
     * Supprimer une recette spécifique de la BDD
     *
     * @param Recette $recette
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteRecette(
        Recette $recette,
        EntityManagerInterface $entityManager 
    ) : JsonResponse
    {
        $entityManager->remove($recette);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/recette', name: 'recette.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    /**
     * Ajouter une recette dans la BDD
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createRecette(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        IngredientRepository $ingredientRepository,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ) : JsonResponse
    {
        $recette = $serializer->deserialize($request->getContent(), Recette::class, 'json');
        $recette->setStatus('on');

        $content = $request->toArray();
        $idIngredient = $content['idIngredient'];
        $recipe = $ingredientRepository->find($idIngredient);
        $recette->addRecetteIngredient($recipe);        

        $errors = $validator->validate($recette);
        //dd($errors->count());
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'createRecette']);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette/{id}', name: 'recette.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    /**
     * Mettre à jour une recette de la BDD
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function updateRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        IngredientRepository $ingredientRepository,
        UrlGeneratorInterface $urlGenerator
    ) : JsonResponse
    {
        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );
        $recette->setStatus('on');

        $content = $request->toArray();
        $idIngredient = $content['idIngredient'];

        $recette->addRecetteIngredient($ingredientRepository->find($idIngredient));        

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette/{idRecette}', name: 'recette.getByIngredient', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    #[IsGranted('ROLE_USER', message: 'T\'as pas les droits sale QUEUE')]
    #[ParamConverter("recette", options: ["id" => "idRecette"])]
    public function getRecetteByIngredient(
        int $idRecette,
        RecetteRepository $repository,
        SerializerInterface $serializer,
        Request $request 
    ) : JsonResponse
    {
        $ingrName = $request->get('ingrName');
        $recette = $repository->fondRecetteByIngredient($ingrName);

        
        $jsonRecette = $serializer->serialize($recette, 'json');
        return $recette ? new JsonResponse($jsonRecette, Response::HTTP_OK, [], true):
        new JsonResponse($jsonRecette, Response::HTTP_NOT_FOUND, [], false);
    }
}

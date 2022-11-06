<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    /**
     * Obtenir la liste de toutes les recettes de la BDD
     *
     * @param RecetteRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getAllRecettes(
        RecetteRepository $repository,
        SerializerInterface $serializer,
        Request $request
    ) : JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 50);
        $limit = $limit > 20 ? 20: $limit;

        $recettes = $repository->findWithPagination($page, $limit); //meme chose que $repository->findAll()
        $jsonRecettes = $serializer->serialize($recettes, 'json', ['groups' => "getAllRecettes"]);
        return new JsonResponse($jsonRecettes, 200, [], true);
    }

    #[Route('/api/recette/{idRecette}', name: 'recette.get', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
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
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
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
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
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
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
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

        $content = $request->toArray();
        $idIngredient = $content['idIngredient'];

        $recette->addRecetteIngredient($ingredientRepository->find($idIngredient));        

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette/ingredient/{name}', name: 'recette.getByIngredient', methods: ['GET'])]
    /**
     * Obtenir toutes les recettes associées à un ingrédient (par le nom)
     *
     * @param Request $request
     * @param RecetteRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getRecetteByIngredient(Request $request, RecetteRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $name = $request->get('name');
        $recette = New Recette();
        $recette = $repository->findRecetteByIngredient($name);
        $jsonRecette = $serializer->serialize($recette, 'json', ["groups" => 'getRecette']);
        return New JsonResponse($jsonRecette,Response::HTTP_OK, [],true);
    }
}

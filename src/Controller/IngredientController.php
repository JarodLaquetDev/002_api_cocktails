<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\PictureRepository;
use App\Repository\RecetteRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
//use Symfony\Component\Serializer\SerializerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serialize;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

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
    #[IsGranted("ROLE_USER", message: 'Absence de droits')]
    /**
     * Obtenir la liste des tous les ingredients de la BDD
     *
     * @param IngredientRepository $repository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     */
    public function getAllIngredient(
        IngredientRepository $repository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $idCache = 'getAllIngredient';
        $jsonIngredients = $cache->get($idCache, function(ItemInterface $item) use ($repository, $request, $serializer){
            echo "MISE EN CACHE";
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 50);
            $limit = $limit > 20 ? 20: $limit;
            $item->tag("ingredientCache");
            $ingredient =  $repository->findWithPagination($page, $limit);//meme chose que $repository->findAll()
            $context = SerializationContext::create()->setGroups(["getAllIngredients"]);
            return $serializer->serialize($ingredient, 'json', $context);
        });

        return new JsonResponse($jsonIngredients, 200, [], true);
    }

    #[Route('/api/ingredient/{idIngredient}', name: 'ingredient.get', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    #[ParamConverter("ingredient", options: ["id" => "idIngredient"])]
    /**
     * Obtenir les informations d'un ingrédient spécifique de la BDD
     *
     * @param Ingredient $ingredient
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     */ 
    public function getIngredient(
        Ingredient $ingredient,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredients = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredients, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/ingredient/{idIngredient}', name: 'ingredient.delete', methods: ['DELETE'])]
    #[ParamConverter("ingredient", options: ["id" => "idIngredient"])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer un ingrédient spécifique de la BDD
     *
     * @param Ingredient $ingredient
     * @param EntityManagerInterface $entityManager
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     */
    public function deleteIngredient(
        Ingredient $ingredient,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache 
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);
        $ingredient->setStatus("off");
        $entityManager->persist($ingredient);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/ingredient', name: 'ingredient.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter un ingrédient dans la BDD
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param RecetteRepository $recetteRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     * @OA\RequestBody(
     *      description= "Ajouter un ingrédient dans la BDD",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="ingredient_name", type="string"),
     *          @OA\Property(property="ingredient_quantity", type="int")
     *      )
     * )
     */
    public function createIngredient(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        RecetteRepository $recetteRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache,
        ValidatorInterface $validator
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);
        $ingredient = $serializer->deserialize($request->getContent(), Ingredient::class, 'json');
        $ingredient->setStatus('on');

        $errors = $validator->validate($ingredient);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredient = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/ingredient/{id}', name: 'ingredient.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Mettre à jour un ingrédient de la BDD
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param RecetteRepository $recetteRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     * @OA\RequestBody(
     *      description= "Mettre à jour un ingrédient de la BDD",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="ingredient_name", type="string"),
     *          @OA\Property(property="ingredient_quantity", type="int")
     *      )
     * )
     */
    public function updateIngredient(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        RecetteRepository $recetteRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache,
        ValidatorInterface $validator
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);
        $updateIngredient = $serializer->deserialize(
            $request->getContent(), 
            Ingredient::class, 
            'json'
        );

        $ingredient->setIngredientName($updateIngredient->getIngredientName() ? $updateIngredient->getIngredientName() : $ingredient->getIngredientName());
        $ingredient->setIngredientQuantity($updateIngredient->getIngredientQuantity() ? $updateIngredient->getIngredientQuantity() : $ingredient->getIngredientQuantity());
        $ingredient->setStatus($updateIngredient->getStatus() ? $updateIngredient->getStatus() : $ingredient->getStatus());

        $errors = $validator->validate($ingredient);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredient = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/ingredient_recette_add/{id}', name: 'ingredientRecetteAdd.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une recette à un ingrédient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param RecetteRepository $recetteRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     * @OA\RequestBody(
     *      description= "Ajouter une recette à un ingrédient",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idRecette", type="int")
     *      )
     * )
     */
    public function addRecetteInIngredient(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        RecetteRepository $recetteRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);

        $content = $request->toArray();
        $idRecette = $content['idRecette'];
        $ingredient->addIngredientRecette($recetteRepository->find($idRecette));      

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredient = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/ingredient_recette_delete/{id}', name: 'ingredientRecetteDelete.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer une recette d'un ingrédient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param RecetteRepository $recetteRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     * @OA\RequestBody(
     *      description= "Supprimer une recette liée à un ingrédient",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idRecette", type="int")
     *      )
     * )
     */
    public function deleteRecetteInIngredient(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        RecetteRepository $recetteRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);

        $content = $request->toArray();
        $idRecette = $content['idRecette'];
        $ingredient->removeIngredientRecette($recetteRepository->find($idRecette));      

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredient = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/ingredient_image_add/{id}', name: 'ingredientPictureAdd.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une image à un ingrédient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param PictureRepository $pictureRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     * @OA\RequestBody(
     *      description= "Ajouter une image à un ingrédient",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idPicture", type="int")
     *      )
     * )
     */
    public function addPictureInIngredient(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        PictureRepository $pictureRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);

        $content = $request->toArray();
        $idPicture = $content['idPicture'];
        $ingredient->setIngredientImage($pictureRepository->find($idPicture));      

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredient = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/ingredient_image_delete/{id}', name: 'ingredientPictureDelete.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer une image d'un ingrédient
     *
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param PictureRepository $pictureRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Ingredients")
     * @OA\RequestBody(
     *      description= "Supprimer une image associée à un ingrédient",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idPicture", type="int")
     *      )
     * )
     */
    public function deletePictureInIngredient(
        Ingredient $ingredient,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer, 
        PictureRepository $pictureRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["ingredientCache"]);

        $ingredient->setIngredientImage(null);      

        $entityManager->persist($ingredient);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("ingredient.get", ['idIngredient' => $ingredient->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getIngredient"]);
        $jsonIngredient = $serializer->serialize($ingredient, 'json', $context);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }
    
}

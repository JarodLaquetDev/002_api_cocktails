<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Repository\PictureRepository;
use App\Repository\RecetteRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstructionRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serialize;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

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

    #[Route('/api/recettes', name: 'recette.getAll', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    /**
     * Obtenir la liste de toutes les recettes de la BDD
     *
     * @param RecetteRepository $repository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     */
    public function getAllRecettes(
        RecetteRepository $repository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $idCache = 'getAllRecette';
        $jsonRecette = $cache->get($idCache, function(ItemInterface $item) use ($repository, $request, $serializer){
            echo "MISE EN CACHE";
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 50);
            $limit = $limit > 20 ? 20: $limit;
            $item->tag("recetteCache");
            $recette = $repository->findWithPagination($page, $limit);//meme chose que $repository->findAll()
            $context = SerializationContext::create()->setGroups(["getAllRecettes"]);
            return $serializer->serialize($recette, 'json', $context);
        });

        return new JsonResponse($jsonRecette, 200, [], true);
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
     * @OA\Tag(name="Recettes")
     */
    public function getRecette(
        Recette $recette,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecettes = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecettes, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/recette/{idRecette}', name: 'recette.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    #[ParamConverter("recette", options: ["id" => "idRecette"])]
    /**
     * Supprimer une recette spécifique de la BDD
     *
     * @param Recette $recette
     * @param EntityManagerInterface $entityManager
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     */
    public function deleteRecette(
        Recette $recette,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);
        $recette->setStatus("off");
        $entityManager->persist($recette);
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
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Ajouter une recette dans la BDD",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="recette_name", type="string")
     *      )
     * )
     */
    public function createRecette(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        IngredientRepository $ingredientRepository,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);
        $recette = $serializer->deserialize($request->getContent(), Recette::class, 'json');
        $recette->setStatus('on');   

        $errors = $validator->validate($recette);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecettes = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecettes, Response::HTTP_CREATED, ["Location" => $location], true);
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
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Mettre à jour une recette de la BDD",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="recette_name", type="string")
     *      )
     * )
     */
    public function updateRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        IngredientRepository $ingredientRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);
        
        $updateRecette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json'
        );

        $recette->setRecetteName($updateRecette->getRecetteName() ? $updateRecette->getRecetteName() : $recette->getRecetteName());
        $recette->setStatus($updateRecette->getStatus() ? $updateRecette->getStatus() : $recette->getStatus());


        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    
    #[Route('/api/recette_ingredient_add/{id}', name: 'recetteIngredientAdd.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter un ingrédient à une recette
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Ajouter un ingrédient à une recette",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idIngredient", type="int")
     *      )
     * )
     */
    public function addIngredientInRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        IngredientRepository $ingredientRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);

        $content = $request->toArray();
        $idIngredient = $content['idIngredient'];
        $recette->addRecetteIngredient($ingredientRepository->find($idIngredient));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette_ingredient_delete/{id}', name: 'recetteIngredientDelete.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer un ingrédient d'une recette
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Supprimer un ingrédient d'une recette",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idIngredient", type="int")
     *      )
     * )
     */
    public function deleteIngredientInRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        IngredientRepository $ingredientRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);

        $content = $request->toArray();
        $idIngredient = $content['idIngredient'];
        $recette->removeRecetteIngredient($ingredientRepository->find($idIngredient));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette_instruction_add/{id}', name: 'recetteInstructionAdd.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une instruction à une recette
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Ajouter une instruction à une recette",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idInstruction", type="int")
     *      )
     * )
     */
    public function addInstructionInRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        InstructionRepository $instructionRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache 
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);

        $content = $request->toArray();
        $idInstruction = $content['idInstruction'];
        $recette->addInstructionRecette($instructionRepository->find($idInstruction));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette_instruction_delete/{id}', name: 'recetteInstructionDelete.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer une instruction d'une recette
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Supprimer une instruction d'une recette",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idInstruction", type="int")
     *      )
     * )
     */
    public function deleteInstructionInRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        InstructionRepository $instructionRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);

        $content = $request->toArray();
        $idInstruction = $content['idInstruction'];
        $recette->removeInstructionRecette($instructionRepository->find($idInstruction));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette_image_add/{id}', name: 'recetteImageAdd.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une image à une recette
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Ajouter une image à une recette",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idPicture", type="int")
     *      )
     * )
     */
    public function addPictureInRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        PictureRepository $pictureRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);

        $content = $request->toArray();
        $idPicture = $content['idPicture'];
        $recette->setImageRecette($pictureRepository->find($idPicture));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/recette_image_delete/{id}', name: 'recetteImageDelete.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer une image d'une recette
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param IngredientRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
     * @OA\RequestBody(
     *      description= "Supprimer une image d'une recette",
     *      required= true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="idPicture", type="int")
     *      )
     * )
     */
    public function deletePictureInRecette(
        Recette $recette,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        PictureRepository $pictureRepository,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);
        
        $recette->setImageRecette(null);  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getRecette"]);
        $jsonRecette = $serializer->serialize($recette, 'json', $context);
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
     * @OA\Tag(name="Recettes")
     */
    public function getRecetteByIngredient(Request $request, RecetteRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $name = $request->get('name');
        $recette = New Recette();
        $recette = $repository->findRecetteByIngredient($name);
        // Si une recette est associée à cet ingrédient
        if($recette)
        {
            $context = SerializationContext::create()->setGroups(["getRecette"]);
            $jsonRecette = $serializer->serialize($recette, 'json', $context);
            return New JsonResponse($jsonRecette, Response::HTTP_OK, [], true);
        }
        // Si aucune recette n'est associée à cet ingrédient
        else 
        {
            return New JsonResponse(['message' => 'Aucune recette avec cet ingredient'], Response::HTTP_NOT_FOUND);
        }
    }
}

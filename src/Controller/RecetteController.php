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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Cache\Marshaller\TagAwareMarshaller;
<<<<<<< HEAD
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
=======
>>>>>>> ec8992ce53f1abf8c6245e60f1b4d38cae590f74

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
        $jsonRecette = $cache->get($idCache, function(ItemInterface $item) use ($repository, $serializer){
            echo "MISE EN CACHE";
            $item->tag("recetteCache");
            $recette = $repository->findAll();//meme chose que $repository->findAll()
            return $serializer->serialize($recette, 'json', ['groups' => "getAllRecettes"]);
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
     * @OA\Tag(name="Recettes")
     */
    public function deleteRecette(
        Recette $recette,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["recetteCache"]);
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
     * @OA\Tag(name="Recettes")
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
     * @OA\Tag(name="Recettes")
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

        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
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
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
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
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
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

        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );

        $content = $request->toArray();
        $idIngredient = $content['idIngredient'];
        $recette->removeRecetteIngredient($ingredientRepository->find($idIngredient));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
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
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
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

        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );

        $content = $request->toArray();
        $idInstruction = $content['idInstruction'];
        $recette->addInstructionRecette($instructionRepository->find($idInstruction));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
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
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
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

        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );

        $content = $request->toArray();
        $idInstruction = $content['idInstruction'];
        $recette->removeInstructionRecette($instructionRepository->find($idInstruction));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
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
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
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

        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );

        $content = $request->toArray();
        $idPicture = $content['idPicture'];
        $recette->setImageRecette($pictureRepository->find($idPicture));  

        $entityManager->persist($recette);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $recette->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($recette, "json", ["groups" => 'getRecette']);
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
     * @return JsonResponse
     * @OA\Tag(name="Recettes")
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

        $recette = $serializer->deserialize(
            $request->getContent(), 
            Recette::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $recette]
        );
        
        $recette->setImageRecette(null);  

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
     * @OA\Tag(name="Recettes")
     */
    public function getRecetteByIngredient(Request $request, RecetteRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $name = $request->get('name');
        $recette = New Recette();
        $recette = $repository->findRecetteByIngredient($name);
        // Si une recette est associée à cet ingrédient
        if(!empty($recette))
        {
            $jsonRecette = $serializer->serialize($recette, 'json', ["groups" => 'getRecette']);
            return New JsonResponse($jsonRecette,Response::HTTP_OK, [],true);

        }
        // Si aucune recette n'est associée à cet ingrédient
        else
        {
            return New JsonResponse(['message' => 'Aucune recette avec cet ingredient'], Response::HTTP_NOT_FOUND);
        }
    }
}

<?php

namespace App\Controller;

use App\Entity\Instruction;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\InstructionRepository;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class InstructionController extends AbstractController
{
    #[Route('/instruction', name: 'app_instruction')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/InstructionController.php',
        ]);
    }

    #[Route('/api/instructions', name: 'instructions.getAll', methods: ['GET'])]
    #[IsGranted("ROLE_USER", message: 'Absence de droits')]
    /**
     * Obtenir la liste des toutes les instructions de la BDD
     *
     * @param InstructionRepository $repository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */ 
    public function getAllInstructions(
        InstructionRepository $repository,
        SerializerInterface $serializer,
        Request $request, 
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $idCache = 'getAllInstruction';
        $jsonRecettes = $cache->get($idCache, function(ItemInterface $item) use ($repository, $request, $serializer){
            echo "MISE EN CACHE";
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 50);
            $limit = $limit > 20 ? 20: $limit;
            $item->tag("instructionCache");
            $instruction = $repository->findWithPagination($page, $limit);//meme chose que $repository->findAll()
            return $serializer->serialize($instruction, 'json', ['groups' => "getAllInstructions"]);
        });
        return new JsonResponse($jsonRecettes, 200, [], true);
    }

    #[Route('/api/instruction/{idInstruction}', name: 'instruction.get', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    #[ParamConverter("instruction", options: ["id" => "idInstruction"])]
    /**
     * Obtenir les informations d'une instruction spécifique de la BDD
     *
     * @param Instruction $instruction
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */ 
    public function getInstruction(
        Instruction $instruction,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $jsonInstruction = $serializer->serialize($instruction, 'json', ['groups' => "getInstruction"]);
        return new JsonResponse($jsonInstruction, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/instruction/{idInstruction}', name: 'instruction.delete', methods: ['DELETE'])]
    #[ParamConverter("instruction", options: ["id" => "idInstruction"])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer une instruction spécifique de la BDD
     *
     * @param Instruction $instruction
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deleteInstruction(
        Instruction $instruction,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["instructionCache"]);
        $entityManager->remove($instruction);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/instruction', name: 'instruction.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une instruction dans la BDD
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param RecetteRepository $recetteRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function createInstruction(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ) : JsonResponse
    {
        $instruction = $serializer->deserialize($request->getContent(), Instruction::class, 'json');
        $instruction->setStatus('on');      

        $errors = $validator->validate($instruction);
        //dd($errors->count());
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($instruction);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("instruction.get", ['idInstruction' => $instruction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonInstruction = $serializer->serialize($instruction, "json", ["groups" => 'getInstruction']);
        return new JsonResponse($jsonInstruction, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/instruction/{id}', name: 'instruction.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Mettre à jour une instruction de la BDD
     *
     * @param Instruction $instruction
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function updateInstruction(
        Instruction $instruction,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
    ) : JsonResponse
    {
        $instruction = $serializer->deserialize(
            $request->getContent(), 
            Instruction::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $instruction]
        );
        $entityManager->persist($instruction);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("instruction.get", ['idInstruction' => $instruction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonInstruction = $serializer->serialize($instruction, "json", ["groups" => 'getInstruction']);
        return new JsonResponse($jsonInstruction, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/instruction_recette_add/{id}', name: 'instructionRecetteAdd.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une recette à une instruction
     *
     * @param Instruction $instruction
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function addRecetteInInstruction(
        Instruction $instruction,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        recetteRepository $recetteRepository
    ) : JsonResponse
    {
        $instruction = $serializer->deserialize(
            $request->getContent(), 
            Instruction::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $instruction]
        );

        $content = $request->toArray();
        $idRecette = $content['idRecette'];
        $instruction->addRecette($recetteRepository->find($idRecette));  

        $entityManager->persist($instruction);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("instruction.get", ['idInstruction' => $instruction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonInstruction = $serializer->serialize($instruction, "json", ["groups" => 'getInstruction']);
        return new JsonResponse($jsonInstruction, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/instruction_recette_delete/{id}', name: 'instructionRecetteDelete.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Supprimer une recette d'une instruction
     *
     * @param Instruction $instruction
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function deleteRecetteInInstruction(
        Instruction $instruction,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        recetteRepository $recetteRepository
    ) : JsonResponse
    {
        $instruction = $serializer->deserialize(
            $request->getContent(), 
            Instruction::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $instruction]
        );

        $content = $request->toArray();
        $idRecette = $content['idRecette'];
        $instruction->removeRecette($recetteRepository->find($idRecette));  

        $entityManager->persist($instruction);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("instruction.get", ['idInstruction' => $instruction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonInstruction = $serializer->serialize($instruction, "json", ["groups" => 'getInstruction']);
        return new JsonResponse($jsonInstruction, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{

    #[Route('/user', name: 'app_user')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/users', name: 'user.getAll', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Obtenir la liste de tous les utilisateurs de la BDD
     *
     * @param UserRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @OA\Tag(name="Users")
     */
    public function getAllUsers(
        UserRepository $repository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        // $page = $request->get('page', 1);
        // $limit = $request->get('limit', 50);
        // $limit = $limit > 20 ? 20: $limit;

        // $users = $repository->findWithPagination($page, $limit); //meme chose que $repository->findAll()
        // $jsonRecettes = $serializer->serialize($users, 'json', ['groups' => "getAllUsers"]);
        // return new JsonResponse($jsonRecettes, 200, [], true);

        $idCache = 'getAllUsers';
        $jsonUsers = $cache->get($idCache, function(ItemInterface $item) use ($repository, $serializer){
            echo "MISE EN CACHE";
            $item->tag("usersCache");
            $user = $repository->findAll();
            return $serializer->serialize($user, 'json', ['groups' => "getAllUsers"]);
        });

        return new JsonResponse($jsonUsers, 200, [], true);
    }

    #[Route('/api/user/{idUser}', name: 'users.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    #[ParamConverter("user", options: ["id" => "idUser"])]
    /**
     * Obtenir les informations d'un utilisateur spécifique de la BDD
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @OA\Tag(name="Users")
     */
    public function getUserById(
        User $user,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $jsonUser = $serializer->serialize($user, 'json', ['groups' => "getUser"]);
        return new JsonResponse($jsonUser, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/user/{idUser}', name: 'user.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    #[ParamConverter("user", options: ["id" => "idUser"])]
    /**
     * Supprimer un utilisateur spécifique de la BDD
     *
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @OA\Tag(name="Users")
     */
    public function deleteUser(
        User $user,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["usersCache"]);
        $entityManager->remove($user);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/user', name: 'user.create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter un utilisateur dans la BDD
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UserRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @OA\Tag(name="Users")
     */
    public function createUser(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["usersCache"]);
        
        $user = $serializer->deserialize($request->getContent(), User::class, 'json'); 
        $password = $user->getPassword();
        $username = $user->getUsername();
        $user->setStatus("on");
        $user->setUsername($username.'@'.$password);
        $user->setPassword($password);  
        
        $errors = $validator->validate($user);
        if($errors->count() > 0){
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("users.get", ['idUser' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonUser = $serializer->serialize($user, "json", ["groups" => 'createUser']);
        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/user/{id}', name: 'user.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Mettre à jour un utilisateur de la BDD
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     * @OA\Tag(name="Users")
     */
    public function updateUser(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $cache->invalidateTags(["usersCache"]);
        $user = $serializer->deserialize(
            $request->getContent(), 
            User::class, 
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $entityManager->persist($user);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("recette.get", ['idRecette' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonUser = $serializer->serialize($user, "json", ["groups" => 'getRecette']);
        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}

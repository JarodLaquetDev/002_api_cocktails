<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\Serialize;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;


class PictureController extends AbstractController
{
    #[Route('/picture', name: 'app_picture')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PictureController.php',
        ]);
    }

    #[Route('/api/pictures', name: 'picture.getAll', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    /**
     * Obtenir la liste de toutes les images de la BDD
     *
     * @param PictureRepository $repository
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Pictures")
     * @OA\Response(
     *         response="200",
     *         description="OK",
     *         @Model(type=Picture::class, 
     *         groups={"getAllPictures"}))
     * @OA\Response(
     *         response="404",
     *         description="Not found")
     */
    public function getAllPictures(
        PictureRepository $repository,
        SerializerInterface $serializer,
        Request $request,
        TagAwareCacheInterface $cache
    ) : JsonResponse
    {
        $idCache = 'getAllPicture';
        $jsonPictures = $cache->get($idCache, function(ItemInterface $item) use ($repository, $request, $serializer){
            echo "MISE EN CACHE";
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 50);
            $limit = $limit > 20 ? 20: $limit;
            $item->tag("pictureCache");
            $picture = $repository->findWithPagination($page, $limit);//meme chose que $repository->findAll()
            $context = SerializationContext::create()->setGroups(["getAllPictures"]);
            return $serializer->serialize($picture, 'json', $context);
        });
        return new JsonResponse($jsonPictures, 200, [], true);
    }
 
    #[Route('api/picture/{idPicture}', name:'picture.get', methods:['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    #[ParamConverter("Picture", options : ["id" => "idPicture"])]
    /**
     * Obtenir les informations d'une image sp??cifique de la BDD
     *
     * @param integer $idPicture
     * @param SerializerInterface $serializer
     * @param PictureRepository $pictureRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param Request $request
     * @return JsonResponse
     * @OA\Tag(name="Pictures")
     * @OA\Response(
     *         response="200",
     *         description="OK",
     *         @Model(type=Picture::class, 
     *         groups={"getAllPictures"}))
     * @OA\Response(
     *         response="404",
     *         description="Not found")
     */
    public function getPicture(int $idPicture, SerializerInterface $serializer, PictureRepository $pictureRepository, UrlGeneratorInterface $urlGenerator, Request $request) : JsonResponse
    {
        $picture = $pictureRepository->find($idPicture);
        $relativePath = $picture->getPublicPath() . "/" . $picture->getRealPath();
        $location = $request->getUriForPath('/');
        $location = $location . str_replace("/images", "images", $relativePath);
        if($picture)
        {
            $context = SerializationContext::create()->setGroups(["getPicture"]);
            $jsonPictures = $serializer->serialize($picture, 'json', $context);
            return new JsonResponse($jsonPictures, Response::HTTP_OK, ['accept' => 'json'], true);
        }
        return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
        
    }

    #[Route('/api/picture/{idPicture}', name: 'picture.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    #[ParamConverter("picture", options: ["id" => "idPicture"])]
    /**
     * Supprimer une image sp??cifique de la BDD
     *
     * @param Picture $picture
     * @param EntityManagerInterface $entityManager
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Pictures")
     * @OA\Response(
     *         response="204",
     *         description="No content")
     * @OA\Response(
     *         response="404",
     *         description="Not found")
     */
    public function deletePicture(
        int $idPicture,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $cache,
        PictureRepository $repository 
    ) : JsonResponse
    {
        $cache->invalidateTags(["pictureCache"]);
        $picture = $repository->find($idPicture);
        $entityManager->remove($picture);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/picture', name: 'picture.create', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    /**
     * Ajouter une image dans la BDD
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $urlGenerator
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cache
     * @return JsonResponse
     * @OA\Tag(name="Pictures")
     * @OA\RequestBody(
     *      @OA\MediaType(mediaType="multipart/form-data",
     *          @OA\Schema(ref="#/components/schemas/FileUpload")))
     * @OA\Response(
     *         response="201",
     *         description="Created",
     *         @Model(type=Picture::class, 
     *         groups={"getAllPictures"}))
     * @OA\Response(
     *         response="404",
     *         description="Not found")
     */
    public function createPicture(Request $request, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer, TagAwareCacheInterface $cache): JsonResponse
    {
        $cache->invalidateTags(["pictureCache"]);
        $picture = new Picture();
        $files = $request->files->get('file');
        $picture->setFile($files);
        $picture->setMimeType($files->getClientMimeType());
        $picture->setRealName($files->getClientOriginalName());
        $picture->setStatus("on");
        $picture->setPublicPath("/images/pictures");
        $entityManager->persist($picture);
        $entityManager->flush();

        $location = $urlGenerator->generate("picture.get", ['idPicture' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $context = SerializationContext::create()->setGroups(["getPicture"]);
        $jsonPictures = $serializer->serialize($picture, 'json', $context);
        return new JsonResponse($jsonPictures, Response::HTTP_CREATED, ["Location" => $location], true);
    }

}

<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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

    #[Route('/api/pictures', name: 'picture.getAll')]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    /**
     * Obtenir la liste de toutes les images de la BDD
     *
     * @param PictureRepository $repository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getAllPictures(
        PictureRepository $repository,
        SerializerInterface $serializer 
    ) : JsonResponse
    {
        $pictures = $repository->findAll();
        $jsonPictures = $serializer->serialize($pictures, 'json', ['groups' => "getAllPictures"]);
        return new JsonResponse($jsonPictures, 200, [], true);
    }

    #[Route('api/picture/{idPicture}', name:'picture.get', methods:['GET'])]
    #[IsGranted('ROLE_USER', message: 'Absence de droits')]
    #[ParamConverter("Picture", options : ["id" => "idPicture"])]
    /**
     * Obtenir les informations d'une image spécifique de la BDD
     *
     * @param integer $idPicture
     * @param SerializerInterface $serializer
     * @param PictureRepository $pictureRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @param Request $request
     * @return JsonResponse
     */
    public function getPicture(int $idPicture, SerializerInterface $serializer, PictureRepository $pictureRepository, UrlGeneratorInterface $urlGenerator, Request $request) : JsonResponse
    {
        $picture = $pictureRepository->find($idPicture);

        $relativePath = $picture->getPublicPath() . "/" . $picture->getRealPath();
        $location = $request->getUriForPath('/');
        $location = $location . str_replace("/images", "images", $relativePath);
        if($picture)
        {
            return new JsonResponse($serializer->serialize($picture, 'json', ["groups" => "getPicture"]), Response::HTTP_OK, ["Location" => $location], true);
        }
        return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
    }

    #[Route('/api/picture/{idPicture}', name: 'picture.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    #[ParamConverter("picture", options: ["id" => "idPicture"])]
    /**
     * Supprimer une image spécifique de la BDD
     *
     * @param Picture $picture
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    public function deletePicture(
        Picture $picture,
        EntityManagerInterface $entityManager 
    ) : JsonResponse
    {
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
     * @return JsonResponse
     */
    public function createPicture(Request $request, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer): JsonResponse
    {
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
        $jsonIngredient = $serializer->serialize($picture, "json", ["groups" => 'getPicture']);
        return new JsonResponse($jsonIngredient, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/picture/{idPicture}', name: 'picture.update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Absence de droits')]
    #[ParamConverter("picture", options : ["id" => "idPicture"])]
    /**
     * Mettre à jour une image de la BDD
     * @param integer $idPicture
     * @param Picture $picture
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param PictureRepository $ingredientRepository
     * @param UrlGeneratorInterface $urlGenerator
     * @return JsonResponse
     */
    public function updatePicture(
        int $idPicture,
        Picture $picture,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator,
        PictureRepository $pictureRepository
    ) : JsonResponse
    {
        $picture = $pictureRepository->find($idPicture);  
        $files = $request->files->get('file');
        $picture->setFile($files);
        //$picture->setMimeType($files->getClientMimeType());
        //$picture->setRealName($files->getClientOriginalName());
        $picture->setStatus("on");
        $picture->setPublicPath("/images/pictures");

        $entityManager->persist($picture);
        $entityManager->flush();
        
        $location = $urlGenerator->generate("picture.get", ['idPicture' => $picture->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $jsonRecette = $serializer->serialize($picture, "json", ["groups" => 'getPicture']);
        return new JsonResponse($jsonRecette, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}

<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
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

    #[Route('api/picture/{idPicture}', name:'picture.get', methods:['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
    #[IsGranted('ROLE_USER', message: 'T\'as pas les droits sale QUEUE')]
    #[ParamConverter("Picture", options : ["id" => "idPicture"])]
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


    #[Route('api/picture', name: 'picture.create', methods:['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'T\'as pas les droits sale QUEUE')]
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
}

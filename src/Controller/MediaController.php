<?php

namespace App\Controller;

use App\Entity\CustomMedia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class MediaController extends AbstractController
{
    #[Route('/', name: 'app_media')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MediaController.php',
        ]);
    }
    #[Route('/api/media', name: 'media.picture', methods: ["POST"])]
    public function createMedia(Request $resquest, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $media = new CustomMedia();
        $files = $resquest->files->get("media");
        $media->setMedia($files);
        $media->setPublicPath('/public/docs/medias');
        $entityManager->persist($media);
        $entityManager->flush();

        $jsonMedia = $serializer->serialize($media, "json");

        $location = $urlGenerator->generate("media.get", ["id" => $media->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonMedia, Response::HTTP_CREATED, ["Location" => $location], true);

    }


    #[Route('/api/media/{id}', name: 'media.get', methods: ["GET"])]
    public function getMedia(CustomMedia $media, Request $resquest, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator)
    {
        $location = $media->getPublicPath() . '/' . $media->getRealPath();
        $location = $urlGenerator->generate("app_media", [], UrlGeneratorInterface::ABSOLUTE_URL);
        $location = $location . str_replace("/public/", "", $media->getPublicPath()) . "/" . $media->getRealPath();
        return $media ? new JsonResponse($media, Response::HTTP_OK, ["Location" => $location], false) : new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }
}

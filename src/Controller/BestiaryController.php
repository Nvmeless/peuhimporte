<?php

namespace App\Controller;

use App\Entity\Bestiary;
use App\Repository\BestiaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
#[Route('/api/bestiary', name: 'api_bestiary')]

final class BestiaryController extends AbstractController
{
    #[Route(path: '/', name: '_index', methods: ["GET"])]
    public function index(BestiaryRepository $repository, SerializerInterface $serializer): JsonResponse
    {

        $bestiaries = $repository->findAll();
        $jsonBestiaries = $serializer->serialize($bestiaries, 'json', ['groups' => "bestiaries"]);

        return new JsonResponse($jsonBestiaries, JsonResponse::HTTP_OK, [], true);
    }
    #[Route('/{id}', name: '_get', methods: ["GET"])]
    public function get(Bestiary $bestiary, SerializerInterface $serializer): JsonResponse
    {



        $jsonBestiaries = $serializer->serialize($bestiary, 'json');

        return new JsonResponse($jsonBestiaries, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/', name: '_new', methods: ["POST"])]
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {


        $bestiary = $serializer->deserialize($request->getContent(), Bestiary::class, 'json');

        // $content = $request->toArray();
        // $bestiary = new Bestiary();
        // $bestiary->setName($content['name'] ?? "Nom Default");
        // $bestiary->setMaxLifePoint($content['pvMax'] ?? 255);
        // $bestiary->setMinLifePoint($content['pvMin'] ?? 1);

        $entityManager->persist($bestiary);
        $entityManager->flush();

        $jsonBestiaries = $serializer->serialize($bestiary, 'json');

        $location = $urlGenerator->generate("api_bestiary_get", ["id" => $bestiary->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonBestiaries, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: '_delete', methods: ["DELETE"])]
    public function delete(Bestiary $bestiary, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($bestiary);
        $entityManager->flush();


        $jsonBestiaries = $serializer->serialize($bestiary, 'json');

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, [], false);
    }

    #[Route('/{id}', name: '_update', methods: ["PUT", "PATCH"])]
    public function update(Request $request, Bestiary $bestiary, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $bestiary = $serializer->deserialize(
            $request->getContent(),
            Bestiary::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $bestiary]
        );

        $entityManager->flush();


        // $jsonBestiaries = $serializer->serialize($bestiary, 'json');

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, [], false);
    }
}

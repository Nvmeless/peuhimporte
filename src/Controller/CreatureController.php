<?php

namespace App\Controller;

use App\Entity\Creature;
use App\Repository\BestiaryRepository;
use App\Repository\CreatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route(path: '/api/creature', name: 'creature')]

final class CreatureController extends AbstractController
{
    #[Route('/', name: 'app_creature', methods: ["GET"])]
    public function index(TagAwareCacheInterface $cache, CreatureRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $idCache = "getAllCreatures";
        $jsonCreatures = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer) {
            echo "MISE EN CACHE ";
            $item->tag('creatureCache');
            $creatures = $repository->findAll();
            return $serializer->serialize($creatures, 'json', ["groups" => "creatures"]);

        });
        // $creatures = $repository->findAll();

        return new JsonResponse($jsonCreatures, JsonResponse::HTTP_OK, [], true);
    }
    #[Route('/{id}', name: "creature_get", methods: ["GET"])]
    #[IsGranted('ROLE_ADMIN')]
    // #[Security("is_granted('ROLE_ADMIN') and is_granted('ROLE_USER')")]
    public function get(Creature $creature, SerializerInterface $serializer): JsonResponse
    {



        $jsonCreatures = $serializer->serialize($creature, 'json', ['groups' => "creatures"]);

        return new JsonResponse($jsonCreatures, JsonResponse::HTTP_OK, [], true);
    }

    // #[Route('/creature', name: 'creature_add', methods: ["POST"])]
    // public function add(TagAwareCacheInterface $cache, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    // {


    //     $creature = $serializer->deserialize($request->getContent(), Creature::class, 'json');

    //     // $content = $request->toArray();
    //     // $creature = new Creature();
    //     // $creature->setName($content['name'] ?? "Nom Default");
    //     // $creature->setMaxLifePoint($content['pvMax'] ?? 255);
    //     // $creature->setMinLifePoint($content['pvMin'] ?? 1);

    //     $entityManager->persist($creature);
    //     $entityManager->flush();

    //     $jsonCreatures = $serializer->serialize($creature, 'json');

    //     $location = $urlGenerator->generate("creature_get", ["id" => $creature->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

    //     $cache->invalidateTags(["getAllCreatures"]);
    //     return new JsonResponse($jsonCreatures, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    // }
    #[Route('/new', name: 'creature_generate', methods: ["POST"])]
    public function generate(BestiaryRepository $bestiaryRepository, TagAwareCacheInterface $cache, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator): JsonResponse
    {


        $creature = $serializer->deserialize($request->getContent(), Creature::class, 'json');

        $content = $request->toArray();
        // $creature = new Creature();

        $bestiary = $bestiaryRepository->find($content["bestiary"]);
        $maxLp = rand($bestiary->getMinLifePoint(), $bestiary->getMaxLifePoint());


        $creature->setName($content['name'] ?? $bestiary->getName());
        $creature->setMaxLifePoint($maxLp);
        $creature->setLifePoint($maxLp);
        $creature->setBestiary($bestiary);

        $errors = $validator->validate($creature);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($creature);
        $entityManager->flush();

        $jsonCreatures = $serializer->serialize($creature, 'json', ['groups' => "creatures"]);

        $location = $urlGenerator->generate("creaturecreature_get", ["id" => $creature->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        $cache->invalidateTags(["creatureCache"]);
        return new JsonResponse($jsonCreatures, JsonResponse::HTTP_CREATED, ["Location" => $location], true);
    }
    #[Route('/{id}', name: 'creature_delete', methods: ["DELETE"])]
    public function delete(TagAwareCacheInterface $cache, Creature $creature, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($creature);
        $entityManager->flush();


        $jsonCreatures = $serializer->serialize($creature, 'json');
        $cache->invalidateTags(["getAllCreatures"]);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, [], false);
    }

    #[Route('/{id}', name: 'creature_update', methods: ["PUT", "PATCH"])]
    public function update(TagAwareCacheInterface $cache, Request $request, Creature $creature, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $creature = $serializer->deserialize(
            $request->getContent(),
            Creature::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $creature]
        );

        $entityManager->flush();

        $cache->invalidateTags(["getAllCreatures"]);

        // $jsonCreatures = $serializer->serialize($creature, 'json');

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT, [], false);
    }
}

<?php

namespace App\Serializer\Normalizer;

use ReflectionClass;
use App\Entity\Bestiary;
use App\Entity\Creature;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AutoDiscoveryNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private NormalizerInterface $normalizer,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        $className = (new ReflectionClass($object))->getShortName();
        $className = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
        $data["_links"] = [
            "up" => [
                "method" => ['GET'],
                "path" => $this->urlGenerator->generate("api_" . $className . "_index")
            ],
            "self" => [
                "method" => ['GET'],
                "path" => $this->urlGenerator->generate("api_" . $className . "_get", ["id" => $data['id']])
            ],
            "new" => [
                "path" => $this->urlGenerator->generate("api_" . $className . "_new"),
                "method" => ["POST"]
            ],
            "delete" => [
                "path" => $this->urlGenerator->generate("api_" . $className . "_delete", ["id" => $data["id"]]),
                "method" => ["DELETE"]
            ],
            "update" => [
                $this->urlGenerator->generate("api_" . $className . "_update", ["id" => $data["id"]]),
                "method" => ["PUT", "PATCH"]
            ],
        ];
        // TODO: add, edit, or delete some data

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        // TODO: return $data instanceof Object
        return ($data instanceof Creature || $data instanceof Bestiary) && $format === "json";
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Creature::class => true,
            Bestiary::class => true,
        ];
    }
}

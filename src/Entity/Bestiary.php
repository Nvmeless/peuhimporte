<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BestiaryRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BestiaryRepository::class)]
class Bestiary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["*", "creatures", "bestiaries"])]



    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["*", "creatures", "bestiaries"])]

    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["*", "creatures", "bestiaries"])]


    private ?int $maxLifePoint = null;

    #[ORM\Column]
    #[Groups(["*", "creatures", "bestiaries"])]

    private ?int $minLifePoint = null;

    #[ORM\ManyToOne(inversedBy: 'bestiaries')]
    #[Groups(["*", "creatures", "bestiaries"])]

    private ?User $createdBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMaxLifePoint(): ?int
    {
        return $this->maxLifePoint;
    }

    public function setMaxLifePoint(int $maxLifePoint): static
    {
        $this->maxLifePoint = $maxLifePoint;

        return $this;
    }

    public function getMinLifePoint(): ?int
    {
        return $this->minLifePoint;
    }

    public function setMinLifePoint(int $minLifePoint): static
    {
        $this->minLifePoint = $minLifePoint;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}

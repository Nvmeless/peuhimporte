<?php

namespace App\Entity;

use App\Repository\CreatureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CreatureRepository::class)]
class Creature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["*", "creatures"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["*", "creatures"])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(
        min: 3,
        max: 25,
        minMessage: 'Your Chimpoko name must be at least {{ limit }} characters long',
        maxMessage: 'Your Chimpoko cannot be longer than {{ limit }} characters',
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["*", "creatures"])]
    private ?int $lifePoint = null;

    #[ORM\Column]
    #[Groups(["*", "creatures"])]
    private ?int $maxLifePoint = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["*", "creatures"])]
    private ?Bestiary $bestiary = null;

    #[ORM\ManyToOne(inversedBy: 'creatures')]
    #[Groups(["*", "creatures"])]

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

    public function getLifePoint(): ?int
    {
        return $this->lifePoint;
    }

    public function setLifePoint(int $lifePoint): static
    {
        $this->lifePoint = $lifePoint;

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

    public function getBestiary(): ?Bestiary
    {
        return $this->bestiary;
    }

    public function setBestiary(?Bestiary $bestiary): static
    {
        $this->bestiary = $bestiary;

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

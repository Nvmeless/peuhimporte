<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["*", "creatures", "bestiaries", "user"])]

    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["*", "creatures", "bestiaries", "user"])]

    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["*", "user"])]

    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Bestiary>
     */
    #[Groups(["*", "user"])]

    #[ORM\OneToMany(targetEntity: Bestiary::class, mappedBy: 'createdBy')]
    private Collection $bestiaries;

    /**
     * @var Collection<int, Creature>
     */
    #[Groups(["*", "user"])]
    #[ORM\OneToMany(targetEntity: Creature::class, mappedBy: 'createdBy')]
    private Collection $creatures;

    public function __construct()
    {
        $this->bestiaries = new ArrayCollection();
        $this->creatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Bestiary>
     */
    public function getBestiaries(): Collection
    {
        return $this->bestiaries;
    }

    public function addBestiary(Bestiary $bestiary): static
    {
        if (!$this->bestiaries->contains($bestiary)) {
            $this->bestiaries->add($bestiary);
            $bestiary->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBestiary(Bestiary $bestiary): static
    {
        if ($this->bestiaries->removeElement($bestiary)) {
            // set the owning side to null (unless already changed)
            if ($bestiary->getCreatedBy() === $this) {
                $bestiary->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Creature>
     */
    public function getCreatures(): Collection
    {
        return $this->creatures;
    }

    public function addCreature(Creature $creature): static
    {
        if (!$this->creatures->contains($creature)) {
            $this->creatures->add($creature);
            $creature->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreature(Creature $creature): static
    {
        if ($this->creatures->removeElement($creature)) {
            // set the owning side to null (unless already changed)
            if ($creature->getCreatedBy() === $this) {
                $creature->setCreatedBy(null);
            }
        }

        return $this;
    }
}

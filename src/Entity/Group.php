<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\State\UserPasswordHasherProcessor;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource(
    normalizationContext: ['groups' => ['group:read']],
    denormalizationContext: ['groups' => ['group:create']],
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['group:read']]),
        new Get(normalizationContext: ['groups' => ['group:read']]),
        new Post(
            processor: UserPasswordHasherProcessor::class,
            denormalizationContext: ['groups' => ['group:create']]
        ),
    ]
)]
class Group implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['group:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['group:read', 'group:create'])]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['group:read', 'group:create'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['group:read'])]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    #[Groups(['group:create'])]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['group:read', 'group:create'])]
    private ?string $city = null;

    /**
     * @var Collection<int, GroupParticipation>
     */
    #[ORM\OneToMany(mappedBy: 'groupUser', targetEntity: GroupParticipation::class, orphanRemoval: true)]
    private Collection $groupParticipations;

    public function __construct()
    {
        $this->groupParticipations = new ArrayCollection();
    }

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nettoyage de données sensibles temporaires si besoin
    }

    /**
     * @return Collection<int, GroupParticipation>
     */
    public function getGroupParticipations(): Collection
    {
        return $this->groupParticipations;
    }

    public function addGroupParticipation(GroupParticipation $groupParticipation): static
    {
        if (!$this->groupParticipations->contains($groupParticipation)) {
            $this->groupParticipations->add($groupParticipation);
            $groupParticipation->setGroupUser($this);
        }

        return $this;
    }

    public function removeGroupParticipation(GroupParticipation $groupParticipation): static
    {
        if ($this->groupParticipations->removeElement($groupParticipation)) {
            if ($groupParticipation->getGroupUser() === $this) {
                $groupParticipation->setGroupUser(null);
            }
        }

        return $this;
    }
}
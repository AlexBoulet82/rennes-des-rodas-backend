<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')] // Bonne pratique : évite les conflits avec le mot-clé réservé SQL "group"
#[ApiResource]
class Group implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
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

    /**
     * Identifiant unique pour la sécurité (l'email)
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantie que chaque groupe a au moins ROLE_USER
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
        // Si tu stockes des données temporaires sensibles, efface-les ici
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
            // set the owning side to null (unless already changed)
            if ($groupParticipation->getGroupUser() === $this) {
                $groupParticipation->setGroupUser(null);
            }
        }

        return $this;
    }
}
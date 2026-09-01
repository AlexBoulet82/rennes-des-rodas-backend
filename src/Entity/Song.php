<?php

namespace App\Entity;
use ApiPlatform\Metadata\Patch; // <--- Ajoutez cette ligne en haut du fichier
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\SongRepository;
use App\State\SongStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor; // <--- Importez ceci

#[ORM\Entity(repositoryClass: SongRepository::class)]
#[ApiResource(
    processor: SongStateProcessor::class,
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['song:read']]),
        new Post(normalizationContext: ['groups' => ['song:read'], 'denormalizationContext' => ['groups' => ['song:write']]]),
        new Get(normalizationContext: ['groups' => ['song:read']]),
        new Put(normalizationContext: ['groups' => ['song:read'], 'denormalizationContext' => ['groups' => ['song:write']]]),
        new Patch(), // <--- Vérifiez que celle-ci est bien là
        new Delete(processor: RemoveProcessor::class)
    ],
    normalizationContext: ['groups' => ['song:read']]
)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['song:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['song:read', 'song:write', 'group:read','participation:read'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['song:read', 'song:write', 'group:read'])]
    private ?string $artist = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $ton = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $notes = null;

    #[ORM\ManyToOne(inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['song:read'])]
    private ?Group $grupo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(?string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getTon(): ?string
    {
        return $this->ton;
    }

    public function setTon(?string $ton): static
    {
        $this->ton = $ton;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getGrupo(): ?Group
    {
        return $this->grupo;
    }

    public function setGrupo(?Group $grupo): static
    {
        $this->grupo = $grupo;

        return $this;
    }
}
<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\GroupParticipationRepository;
use App\State\GroupParticipationProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\GroupParticipationCsvController;

#[ORM\Entity(repositoryClass: GroupParticipationRepository::class)]
#[ApiResource(
    // ⚙️ Configuration de la pagination
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationMaximumItemsPerPage: 100,
    paginationItemsPerPage: 15,
    // 🚦 Configuration des opérations
  operations: [
        // 🟢 NOUVELLE OPÉRATION POUR LE CSV EXPORT
     new GetCollection(
            uriTemplate: '/group_participations.csv',
            controller: GroupParticipationCsvController::class,
            read: false, // Le contrôleur gère lui-même la récupération des données
            normalizationContext: ['groups' => ['participation:csv']],
            
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['participation:read', 'participation:csv']]
        ),
        new Get(),
        new Post(
            processor: GroupParticipationProcessor::class,
            denormalizationContext: ['groups' => ['participation:create']]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "Seul un administrateur peut modifier une participation.",
            inputFormats: ['json' => ['application/json', 'application/merge-patch+json']],
            denormalizationContext: ['groups' => ['participation:admin:write']]
        ),
        new Patch(
            uriTemplate: '/group_participations/{id}/cancel',
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getGroupUser() == user)",
            securityMessage: "Vous ne pouvez annuler que vos propres participations.",
            inputFormats: ['json' => ['application/json', 'application/merge-patch+json']],
            denormalizationContext: ['groups' => ['participation:cancel']]
        )
    ],
    normalizationContext: ['groups' => ['participation:read']]
)]
// 🔍 Filtres de recherche, tri et date
#[ApiFilter(SearchFilter::class, properties: [
    'status' => 'exact',
    'edition' => 'exact',
    'repertoire' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id' => 'ASC',
    'status' => 'ASC',
    'createdAt' => 'DESC',
    'edition.year' => 'DESC'
], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
class GroupParticipation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['participation:read', 'participation:csv'])] // 🟢 Ajout du groupe CSV pour avoir l'ID dans le tableau
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['participation:read', 'participation:csv'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(
        choices: ['pending', 'accepted', 'rejected', 'canceled'],
        message: 'Le statut "{{ value }}" n\'est pas valide. Les valeurs autorisées sont : {{ choices }}.'
    )]
    #[Groups(['participation:read', 'participation:admin:write', 'participation:cancel', 'participation:csv'])]
    private ?string $status = 'pending';

    #[ORM\Column(length: 255, nullable: true)] // 🟢 Champ optionnel
    #[Assert\Length(
        min: 3, 
        max: 255, 
        minMessage: 'Le répertoire doit contenir au moins {{ limit }} caractères si vous le renseignez.' // 🔧 Correction du placeholder
    )]
    #[Groups(['participation:read', 'participation:create', 'participation:admin:write', 'participation:csv'])]
    private ?string $repertoire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 2000)]
    #[Groups(['participation:read', 'participation:create', 'participation:admin:write', 'participation:csv'])]
    private ?string $technicalNeeds = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000)]
    #[Groups(['participation:read', 'participation:create', 'participation:admin:write', 'participation:csv'])]
    private ?string $specialRequests = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'L\'édition doit être renseignée.')]
    #[Groups(['participation:read', 'participation:create', 'participation:admin:write'])]
    private ?Edition $edition = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'groupParticipations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['participation:read'])]
    private ?Group $groupUser = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRepertoire(): ?string
    {
        return $this->repertoire;
    }

    public function setRepertoire(?string $repertoire): static
    {
        $this->repertoire = $repertoire;

        return $this;
    }

    public function getTechnicalNeeds(): ?string
    {
        return $this->technicalNeeds;
    }

    public function setTechnicalNeeds(?string $technicalNeeds): static
    {
        $this->technicalNeeds = $technicalNeeds;

        return $this;
    }

    public function getSpecialRequests(): ?string
    {
        return $this->specialRequests;
    }

    public function setSpecialRequests(?string $specialRequests): static
    {
        $this->specialRequests = $specialRequests;

        return $this;
    }

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition): static
    {
        $this->edition = $edition;

        return $this;
    }

    public function getGroupUser(): ?Group
    {
        return $this->groupUser;
    }

    public function setGroupUser(?Group $groupUser): static
    {
        $this->groupUser = $groupUser;

        return $this;
    }
}
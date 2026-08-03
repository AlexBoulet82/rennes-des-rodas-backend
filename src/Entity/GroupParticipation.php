<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Edition;
use App\Entity\Group;
use App\Repository\GroupParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupParticipationRepository::class)]
#[ApiResource]
class GroupParticipation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $repertoire = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $technicalNeeds = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $specialRequests = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Edition $edition = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'groupParticipations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $groupUser = null;

    public function getId(): ?int
    {
        return $this->id;
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
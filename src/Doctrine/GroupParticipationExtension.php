<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Group;
use App\Entity\GroupParticipation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class GroupParticipationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security
    ) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        // 1. On vérifie qu'on cible bien l'entité GroupParticipation
        if (GroupParticipation::class !== $resourceClass) {
            return;
        }

        // 2. On récupère l'utilisateur actuellement connecté
        $user = $this->security->getUser();

        // 3. Si l'utilisateur est un administrateur (ROLE_ADMIN), on ne filtre pas (il voit tout)
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        // 4. Si c'est un groupe connecté, on filtre sur son propre ID
        if ($user instanceof Group) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->andWhere(sprintf('%s.groupUser = :currentUser', $rootAlias))
                ->setParameter('currentUser', $user);
            return;
        }

        // 5. Si la personne n'est ni Admin ni Groupe (ex: utilisateur anonyme), on ne renvoie rien
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.id IS NULL', $rootAlias));
    }
}
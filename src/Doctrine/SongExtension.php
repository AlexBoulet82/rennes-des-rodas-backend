<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Song;
use App\Entity\Group;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class SongExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private Security $security) {}

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if (Song::class !== $resourceClass) {
            return;
        }

        $user = $this->security->getUser();

        if ($user instanceof Group) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.grupo = :current_group', $rootAlias))
                         ->setParameter('current_group', $user);
        }
    }
}
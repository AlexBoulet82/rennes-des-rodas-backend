<?php 
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroupParticipation;
use App\Entity\Group;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GroupParticipationProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        // On vérifie que la donnée traitée est bien une participation
        if ($data instanceof GroupParticipation) {
            $currentUser = $this->security->getUser();

            // S'assurer qu'un groupe/utilisateur est bien connecté
            if (!$currentUser instanceof Group) {
                throw new UnauthorizedHttpException('Bearer', 'Vous devez être connecté en tant que groupe pour effectuer cette action.');
            }

            // Attrition automatique de l'utilisateur connecté
            $data->setGroupUser($currentUser);
        }

        // On passe la main au processeur de persistance par défaut de Doctrine
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
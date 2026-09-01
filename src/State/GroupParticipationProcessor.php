<?php 
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\GroupParticipation;
use App\Entity\Group;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GroupParticipationProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof GroupParticipation) {
            /** @var Group|null $currentUser */
            $currentUser = $this->security->getUser();

            if (!$currentUser instanceof Group) {
                throw new UnauthorizedHttpException('Bearer', 'Vous devez être connecté en tant que groupe pour effectuer cette action.');
            }

            $edition = $data->getEdition();

            // 1. Récupération de la requête brute pour extraire les IRI de "songs" si le denormalizer ne l'a pas fait
            $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
            $content = json_decode($request->getContent(), true);
            $songIris = $content['songs'] ?? [];

            // 2. Vérifier si une participation existe déjà pour ce groupe et cette édition
            $existingParticipation = $this->entityManager->getRepository(GroupParticipation::class)->findOneBy([
                'groupUser' => $currentUser,
                'edition' => $edition
            ]);

            $targetParticipation = $existingParticipation ?: $data;

            if (!$existingParticipation) {
                $targetParticipation->setGroupUser($currentUser);
            }

            // Mise à jour des champs de base
            $targetParticipation->setStatus($data->getStatus());
            $targetParticipation->setTechnicalNeeds($data->getTechnicalNeeds());
            $targetParticipation->setSpecialRequests($data->getSpecialRequests());

            // 3. Association manuelle et propre des morceaux via leurs IDs extraits des IRIs
            $targetParticipation->getSongs()->clear();
            foreach ($songIris as $iri) {
                // Extrait l'ID de l'IRI (ex: "/api/songs/6" -> 6)
                $id = (int) basename($iri);
                $song = $this->entityManager->getRepository(Song::class)->find($id);
                if ($song) {
                    $targetParticipation->addSong($song);
                }
            }

            $data = $targetParticipation;
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
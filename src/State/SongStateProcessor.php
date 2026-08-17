<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Entity\Song;

class SongStateProcessor implements ProcessorInterface
{
    private ProcessorInterface $decorated;
    private Security $security;

    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        ProcessorInterface $decorated,
        Security $security
    ) {
        $this->decorated = $decorated;
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof Song && $operation instanceof Post) {
            $user = $this->security->getUser();
            if ($user) {
                $data->setGrupo($user);
            }
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}
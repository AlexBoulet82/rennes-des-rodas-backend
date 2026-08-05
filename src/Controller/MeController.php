<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\Group;

class MeController extends AbstractController
{
    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(#[CurrentUser] ?Group $group): JsonResponse
    {
        if (!$group) {
            return $this->json(['message' => 'Non authentifié'], 401);
        }

        return $this->json($group, 200, [], ['groups' => 'group:read']);
    }
}
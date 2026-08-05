<?php

namespace App\Controller;

use App\Repository\GroupParticipationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupParticipationCsvController extends AbstractController
{
    public function __invoke(Request $request, GroupParticipationRepository $repository): Response
    {
        // 1. Récupérer le paramètre 'withRepertoire' (true par défaut)
        $withRepertoire = $request->query->getBoolean('withRepertoire', true);

        // 2. Récupérer les participations
        $participations = $repository->findAll();

        // 3. Construction des données CSV
        $csvData = [];

        $headers = ['ID', 'Statut', 'Nom du groupe', 'Besoins techniques', 'Demandes spéciales'];
        if ($withRepertoire) {
            $headers[] = 'Répertoire';
        }
        $csvData[] = $headers;

        foreach ($participations as $participation) {
            $groupName = $participation->getGroupUser() ? $participation->getGroupUser()->getName() : 'Inconnu';

            $row = [
                $participation->getId(),
                $participation->getStatus(),
                $groupName,
                $participation->getTechnicalNeeds(),
                $participation->getSpecialRequests(),
            ];

            if ($withRepertoire) {
                $row[] = $participation->getRepertoire();
            }

            $csvData[] = $row;
        }

        // 4. Génération du fichier CSV
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="participations.csv"');

        return $response;
    }
}
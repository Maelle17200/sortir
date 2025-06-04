<?php

namespace App\Controller;

use App\Entity\Lieu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu/info/{id}', name: 'lieu_info')]
    public function info(Lieu $lieu): JsonResponse
    {
        return new JsonResponse([
            'rue' => $lieu->getRue(),
            'codePostal' => $lieu->getVille()?->getCodePostal(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
        ]);
    }
}
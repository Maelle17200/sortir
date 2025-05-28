<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'sortie_liste', methods: ['GET'])]
    public function liste(): Response
    {
        return $this->render('sortie/list.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
}

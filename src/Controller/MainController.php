<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MainController extends AbstractController
{
    #[Route('/', name: 'main_accueil', methods: ['GET'])]
    public function accueil():Response
    {
        return $this->render('main/accueil.html.twig');
    }

    #[Route('/test', name: 'main_test', methods: ['GET'])]
    public function test():Response
    {
        return $this->render('main/test.html.twig');
    }


}
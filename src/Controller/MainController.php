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

    #[Route('/login', name: 'main_login', methods: ['GET'])]
    public function login():Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/modifierProfil', name: 'main_modifierProfil', methods: ['GET'])]
    public function modifierProfil():Response
    {
        return $this->render('main/modifierProfil.html.twig');
    }



}
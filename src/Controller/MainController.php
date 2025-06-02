<?php

namespace App\Controller;

use App\Form\ParticipantForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class MainController extends AbstractController
{
    #[Route('/', name: 'app_main_login_login', methods: ['GET'])]
    public function login():Response
    {
        return $this->render('security/login.html.twig');
    }

    #[Route('/accueil', name: 'main_accueil', methods: ['GET'])]
    public function accueil():Response
    {
        return $this->render('main/accueil.html.twig');
    }

    #[Route('/modifierProfil', name: 'main_modifierProfil', methods: ['GET', 'POST'])]
    public function modifierProfil():Response
    {
        $participant = $this->getUser();
        $form = $this->createForm(ParticipantForm::class, $participant);

        return $this->render('main/modifierProfil.html.twig', [
            'form' => $form->createView()
        ]);
    }





}
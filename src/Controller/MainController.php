<?php

namespace App\Controller;

use App\Form\ParticipantType;
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

    #[Route('/modifierProfil', name: 'main_modifierProfil', methods: ['GET', 'POST'])]
    public function modifierProfil():Response
    {
        $participant = $this->getUser();
        $form = $this->createForm(ParticipantType::class, $participant);

        return $this->render('main/modifierProfil.html.twig', [
            'form' => $form->createView()
        ]);
    }



}
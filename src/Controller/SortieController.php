<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\TriSortiesForm;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class SortieController extends AbstractController
{
    //Affiche et traite le formulaire de tri et la liste des sorties
    #[Route('/sortie', name: 'sortie_liste', methods: ['GET', 'POST'])]
    public function liste(SortieRepository $sr, Request $request,): Response
    {
        $sortie = new Sortie();
        $listeSorties = $sr->findAll();

        $form = $this->createForm(TriSortiesForm::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {




        }

        return $this->render('sortie/list.html.twig', [
            //passe la liste des sorties à twig pour affichage
            'sorties' => $listeSorties,
            //Passe le formulaire à twig pour affichage
            'triSortiesForm' => $form->createView(),
        ]);
    }
}

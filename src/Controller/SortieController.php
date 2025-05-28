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
    #[Route('/sortie', name: 'sortie_liste', methods: ['GET', 'POST'])]
    public function liste(SortieRepository $sr, Request $request,): Response
    {
        $sortie = new Sortie();
        $listeSorties = $sr->findAll();

        $form = $this->createForm(TriSortiesForm::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('sortie_liste');
        }

        return $this->render('sortie/list.html.twig', [
            'sorties' => $listeSorties,
            'triSortiesForm' => $form->createView(),
        ]);
    }
}

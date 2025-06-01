<?php

namespace App\Controller;

use App\DTO\RechercheSortiesDTO;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\RechercheSortiesForm;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function PHPUnit\Framework\isEmpty;

#[Route('/user')]
final class SortieController extends AbstractController
{
    //Affiche et traite le formulaire de tri et la liste des sorties
    #[Route('/sortie', name: 'sortie_liste', methods: ['GET', 'POST'])]
    public function liste(SortieRepository $sr, Request $request, EntityManagerInterface $em): Response
    {
        $rechercheSortie = new RechercheSortiesDTO();
        $listeSorties = [];

        $form = $this->createForm(RechercheSortiesForm::class, $rechercheSortie);
        $form->handleRequest($request);//récupère les données du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $nom = $formData->getNom();
            $dateDebutRecherche = $formData->getDateHeureDebutRecherche();
            $dateFinRecherche = $formData->getDateHeureFinRecherche();
            $campus = $formData->getCampus();
            $tousCampus = $formData->isTousCampus();
            $user = $this->getUser();
            $userOrganisateur = $formData->isUserOrganisateur();
            $userInscrit = $formData->isUserInscrit();
            $userPasInscrit = $formData->isUserPasInscrit();
            $sortiesTerminees = $formData->isSortiesTerminees();

            $listeSorties = $sr->getByRecherche($nom, $dateDebutRecherche, $dateFinRecherche, $campus, $tousCampus, $user, $userOrganisateur, $userInscrit, $userPasInscrit, $sortiesTerminees);

            return $this->render('sortie/list.html.twig', [
                //passe la liste des sorties à twig pour affichage
                'sorties' => $listeSorties,
                //Passe le formulaire à twig pour affichage
                'rechercheSortiesForm' => $form->createView(),
            ]);

        }

        $listeSorties = $sr->findAll();

        return $this->render('sortie/list.html.twig', [
            //passe la liste des sorties à twig pour affichage
            'sorties' => $listeSorties,
            //Passe le formulaire à twig pour affichage
            'rechercheSortiesForm' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\DTO\RechercheSortiesDTO;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\RechercheSortiesForm;
use App\Form\SortieCreatModifForm;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class SortieController extends AbstractController
{
    //Affiche et traite le formulaire de tri et la liste des sorties
    #[Route('/sortie', name: 'sortie_liste', methods: ['GET', 'POST'])]
    public function liste(SortieRepository $sr, Request $request): Response
    {
        $rechercheSortie = new RechercheSortiesDTO();

        $form = $this->createForm(RechercheSortiesForm::class, $rechercheSortie);
        $form->handleRequest($request);//récupère les données du formulaire

        //Traitement du formulaire s'il est soumis et valide
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

            //Appel de la base avec ces données
            $listeSorties = $sr->getByRecherche($nom, $dateDebutRecherche, $dateFinRecherche, $campus, $tousCampus, $user, $userOrganisateur, $userInscrit, $userPasInscrit, $sortiesTerminees);

            //Passe à Twig les sorties filtrées, réaffiche le formulaire
            return $this->render('sortie/list.html.twig', [
                //passe la liste des sorties à twig pour affichage
                'sorties' => $listeSorties,
                //Passe le formulaire à twig pour affichage
                'rechercheSortiesForm' => $form->createView(),
            ]);

        }

        //Si le formulaire n'est pas soumis : affichage de toutes les sorties et du formulaire de recherche
        $listeSorties = $sr->findAll();

        return $this->render('sortie/list.html.twig', [
            //passe la liste des sorties à twig pour affichage
            'sorties' => $listeSorties,
            //Passe le formulaire à twig pour affichage
            'rechercheSortiesForm' => $form->createView(),
        ]);
    }

    #[Route('/sortie/{id}', name: 'sortie_detail', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function detail(SortieRepository $sr, $id): Response
    {
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sr->find($id),
        ]);
    }

    #[Route('/sortie/creer', name: 'sortie_creer', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function creer(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();

        $form = $this->createForm(SortieCreatModifForm::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'En création']);
            if (!$etat) {
                throw new \Exception('Etat "En création" introuvable en base de données.');
            }

            $sortie->setOrganisateur($this->getUser());
            $sortie->setEtat($etat);

            if (!$sortie->getLieu()) {
                $this->addFlash('danger', 'Veuillez sélectionner un lieu pour la sortie.');
                return $this->render('sortie/creer.html.twig', [
                    'sortie' => $sortie,
                    'form' => $form->createView(),
                ]);
            }

            $em->persist($sortie);
            $em->flush();

            $this->addFlash("success", "La sortie a bien été créée");

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/creer.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sortie/publier', name: 'sortie_publier', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function publier(Request $request, EntityManagerInterface $em, Sortie $sortie): Response
    {
        $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
        if (!$etat) {
            throw new \Exception('Etat "Ouverte" introuvable en base de données.');
        }

        $sortie->setEtat($etat);

        $em->persist($sortie);
        $em->flush();

        $this->addFlash("success", "La sortie est publiée");

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
    }

    #[Route('/sortie/modifier/{id}', name: 'sortie_modifier', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function modifier(Sortie $sortie, Request $request, EntityManagerInterface $em): Response
    {
        // Sécurité : seul l'organisateur peut modifier la sortie
        if ($sortie->getOrganisateur() !== $this->getUser()) {
            $this->addFlash("danger", "Vous n'avez pas le droit de modifier cette sortie.");
            return $this->redirectToRoute('sortie_liste');
        }

        $form = $this->createForm(SortieCreatModifForm::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$sortie->getLieu()) {
                $this->addFlash('danger', 'Veuillez sélectionner un lieu.');
                return $this->render('sortie/modification.html.twig', [
                    'sortie' => $sortie,
                    'form' => $form->createView(),
                ]);
            }

            $action = $request->request->get('action');

            switch ($action) {
                case 'enregistrer':
                    $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'En création']);
                    $sortie->setEtat($etat);
                    $this->addFlash('success', 'Sortie enregistrée en brouillon.');
                    break;

                case 'publier':
                    $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
                    $sortie->setEtat($etat);
                    $this->addFlash('success', 'Sortie publiée.');
                    break;

                case 'supprimer':
                    $em->remove($sortie);
                    $em->flush();
                    $this->addFlash('danger', 'Sortie supprimée.');
                    return $this->redirectToRoute('sortie_liste');

                default:
                    $this->addFlash('warning', 'Action non reconnue.');
            }



            $em->persist($sortie);
            $em->flush();

            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/modification.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

}

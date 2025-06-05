<?php

namespace App\Controller;

use App\DTO\RechercheSortiesDTO;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\AnnulationSortieForm;
use App\Form\RechercheSortiesForm;
use App\Form\SortieCreatModifForm;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Service\HistoriserSortiesService;
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
    public function liste(SortieRepository $sr, Request $request, HistoriserSortiesService $historiser): Response
    {
        $historiser->historiserSorties();
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
            $user = $this->getUser();
            $userOrganisateur = $formData->isUserOrganisateur();
            $userInscrit = $formData->isUserInscrit();
            $userPasInscrit = $formData->isUserPasInscrit();
            $sortiesTerminees = $formData->isSortiesTerminees();

            //Appel de la base avec ces données
            $listeSorties = $sr->getByRecherche($nom, $dateDebutRecherche, $dateFinRecherche, $campus, $user, $userOrganisateur, $userInscrit, $userPasInscrit, $sortiesTerminees);

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

    //Affiche le détail d'une sortie
    #[Route('/sortie/{id}', name: 'sortie_detail', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function detail(SortieRepository $sr, int $id): Response
    {
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sr->find($id),
        ]);
    }

    //Enregistrer une sortie
    #[Route('/sortie/enregistrer', name: 'sortie_enregistrer', methods: ['GET', 'POST'])]
    public function enregistrer(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieCreatModifForm::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $sortie->setOrganisateur($this->getUser());
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'En création']);
            if (!$etat) {
                throw new \Exception('Etat "En création" introuvable en base de données.');
            }
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

            //Si le submit cliqué est "enregistrer" le traitement s'arrête là, renvoie sur le détail de la sortie créée
            if ($form->get('enregistrer')->isClicked()) {
                $this->addFlash("success", "La sortie a bien été créée");
                return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
            }

            //Si le submit est "publier" le traitement continu dans sortie_publier, pour changer l'état.
            if ($form->get('publier')->isClicked()) {
                return $this->redirectToRoute('sortie_publier', ['id' => $sortie->getId()]);
            }
        }

        return $this->render('sortie/creer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //Publier une sortie
    #[Route('/sortie/{id}/publier', name: 'sortie_publier', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function publier(int $id, EntityManagerInterface $em): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);
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

    #[Route('/sortie/{id}/inscrire', name: 'sortie_inscrire', requirements: ['id'=>'\d+'], methods: ['GET','POST'])]
    public function inscrire(int $id, SortieRepository $sr, ParticipantRepository $pr, EntityManagerInterface $em): Response
    {
        $sortie = $sr->find($id);

        if($sortie->getEtat()->getLibelle() == "Ouverte" && $sortie->getNbInscriptionMax() > $sortie->getParticipants()->count()) {
            $newParticipant = $pr->find($this->getUser()->getId());
            $sortie->addParticipant($newParticipant);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Vous avez été inscrit à la sortie" . $sortie->getNom() . ".");
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        } else {
            $this->addFlash("danger", "Impossible de s'inscrire à la sortie" . $sortie->getNom() . ", vérifier son état et le nombre de places disponibles.");
            return $this->redirectToRoute('sortie_liste');
        }

    }

    #[Route('/sortie/{id}/desister', name: 'sortie_desister', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function desister(int $id, SortieRepository $sr, ParticipantRepository $pr, EntityManagerInterface $em): Response
    {
        $sortie = $sr->find($id);

        if($sortie->getEtat()->getLibelle() == "Ouverte" || $sortie->getEtat()->getLibelle() == "Clôturée" ){
            $oldParticipant = $pr->find($this->getUser()->getId());
            $sortie->removeParticipant($oldParticipant);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Vous vous ête désisté pour la sortie" . $sortie->getNom() . ".");
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        } else {
            $this->addFlash("danger", "impossible de se désister, la sortie" . $sortie->getNom() . "n'est pas ouverte. Son statut est : " . $sortie->getEtat()->getLibelle());
            return $this->redirectToRoute('sortie_liste');
        }
    }

    #[Route('/sortie/modifier/{id}', name: 'sortie_modifier', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function modifier(Sortie $sortie, Request $request, EntityManagerInterface $em): Response
    {
        // Sécurité : seul l'organisateur peut modifier la sortie
        if ($sortie->getOrganisateur() !== $this->getUser()) {
            $this->addFlash("danger", "Vous n'avez pas le droit de modifier cette sortie.");
            return $this->redirectToRoute('sortie_liste');
        }

        //Sécurité : la sortie ne peut être modifiée que si elle n'a pas été publiée
        if($sortie->getEtat()->getLibelle() != "En création"){
            $this->addFlash("danger", "Vous n'avez pas le droit de modifier une sortie qui a déjà été publiée.");
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

    #[Route('/sortie/annuler/{id}', name: 'sortie_annuler', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function annuler(EntityManagerInterface $em, Sortie $sortie, Request $request): Response
    {
        if ($sortie->getOrganisateur() !== $this->getUser() && $sortie->getEtat()->getLibelle() !== "Ouverte" || $sortie->getEtat()->getLibelle() !== "Clôturée" ) {
            $this->addFlash("danger", "Vous n'avez pas le droit d'annuler cette sortie.");
            return $this->redirectToRoute('sortie_liste');
        }

        if ($sortie->getDateHeureDebut() > new \DateTime()) {

            $form = $this->createForm(AnnulationSortieForm::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $sortie->setEtat($em->getRepository(Etat::class)->findOneBy(['libelle' => 'Annulée']));

                $em->persist($sortie);
                $em->flush();

                $this->addFlash("warning", "La sortie a été annulée.");

                return $this->redirectToRoute('sortie_liste');
            }

            return $this->render('sortie/annuler.html.twig', [
                'sortie' => $sortie,
                'form' => $form->createView(),
            ]);

        } else {

            $this->addFlash("warning", "Vous ne pouvez pas annuler une sortie qui a déjà commencé.");

            return $this->redirectToRoute('sortie_liste');

        }


    }
}

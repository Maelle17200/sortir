<?php

namespace App\Controller;

use App\DTO\RechercheSortiesDTO;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\RechercheSortiesForm;
use App\Form\SortieCreatModifForm;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
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
            dump($form->getData());

            $sortie->setOrganisateur($this->getUser());
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'En création']);
            if (!$etat) {
                throw new \Exception('Etat "En création" introuvable en base de données.');
            }
            $sortie->setEtat($etat);
            dump($sortie);
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

        $this->addFlash("success", "La sortie a bien été publiée");

        return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);

    }

    #[Route('/sortie/{id}/inscrire', name: 'sortie_inscrire', requirements: ['id'=>'\d+'], methods: ['GET','POST'])]
    public function inscrire(int $id, SortieRepository $sr, ParticipantRepository $pr, EntityManagerInterface $em): Response
    {
        $sortie = $sr->find($id);

        if($sortie->getEtat()->getLibelle() == "Ouverte"){
            $newParticipant = $pr->find($this->getUser()->getId());
            $sortie->addParticipant($newParticipant);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash("success", "Vous avez été inscrit à la sortie" . $sortie->getNom() . ".");
            return $this->redirectToRoute('sortie_detail', ['id' => $sortie->getId()]);
        } else {
            $this->addFlash("danger", "Impossible de s'inscrire, la sortie" . $sortie->getNom() . "n'est pas ouverte. Son statut est : " . $sortie->getEtat()->getLibelle());
            return $this->redirectToRoute('sortie_liste');
        }

    }

    #[Route('/sortie/{id}/desister', name: 'sortie_desister', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function desister(int $id, SortieRepository $sr, ParticipantRepository $pr, EntityManagerInterface $em): Response
    {
        $sortie = $sr->find($id);

        if($sortie->getEtat()->getLibelle() == "Ouverte"){
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
}

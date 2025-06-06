<?php

namespace App\Controller;

use App\Form\ParticipantForm;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Participant;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class ParticipantController extends AbstractController
{
    #[Route('/participant/{id}/modifier', name: 'app_modifier_participant')]
//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(int $id, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, SluggerInterface $slugger): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $participant = $em->getRepository(Participant::class)->find($id);
        $form = $this->createForm(ParticipantForm::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //hashage du mdp
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $participant->setPassword($hasher->hashPassword($participant, $plainPassword));
            }

            //récupération de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $participant->setImageURL($newFilename);

            }

            $em->flush();
            $this->addFlash('success', 'Participant mis à jour !');
            return $this->redirectToRoute('app_modifier_participant');
        }

        return $this->render('main/modifierProfil.html.twig', [
            'form' => $form->createView(),
            'participant' => $participant,
        ]);
    }

    #[Route('/participant/{id}/suppr_img', name: 'participant_suppr_img', methods: ['GET', 'POST'])]
    public function supprImg(Participant $participant, EntityManagerInterface $em): Response
    {
        $imageURL = $this->getParameter('images_directory').'/'.$participant->getImageURL();

        $filesystem = new Filesystem();
        //vérifie que l'image existe
        if ($filesystem->exists($imageURL)) {
            //supprime l'image du dossier upload/img
            $filesystem->remove($imageURL);
        }

        //vérifie la présence d'une URL en base et la supprime
        if($imageURL){
            $participant->setImageURL(null);
        }

        $em->persist($participant);
        $em->flush();

        $this->addFlash('warning', 'L\'image a été supprimée.');

        return $this->redirectToRoute('app_modifier_participant');
    }

    #[Route('/participant/profil/{id}', name: 'app_participant_profil')]
    public function showProfil(Participant $participant): Response
    {
        return $this->render('participant/profil.html.twig', [
            'participant' => $participant
        ]);
    }


}
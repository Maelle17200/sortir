<?php

namespace App\Controller;

use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParticipantController extends AbstractController
{
    #[Route('/participant/modifier', name: 'app_modifier_participant')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ParticipantType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
            }

            $em->flush();
            $this->addFlash('success', 'Participant mis à jour !');
            return $this->redirectToRoute('main_accueil');
        }

        return $this->render('main/modifierProfil.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Ville;
use App\Form\RegistrationForm;
use App\Form\VilleForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/accueil', name: 'admin_accueil', methods: ['GET','POST'])]
    public function accueil(): Response
    {
        return $this->render('admin/admin.html.twig', [

        ]);
    }

    #[Route('/register', name: 'admin_register', methods: ['GET','POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            if ($user->getCampus() === null) {
                $this->addFlash('error', 'Veuillez sélectionner un campus.');
                return $this->redirectToRoute('admin_register');
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main');
        }

        $listeParticipants = $entityManager->getRepository(Participant::class)->findAll();

        return $this->render('admin/register.html.twig', [
            'registrationForm' => $form,
            'listeParticipants' => $listeParticipants,

        ]);
    }

    #[Route('/ville', name: 'admin_ville', methods: ['GET','POST'])]
    public function ville(Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleForm::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($ville);
            $em->flush();

            $this->addFlash("success", "La ville " . $ville->getNom() . " a bien été crée.");
            return $this->redirectToRoute('admin_ville');
        }

        return $this->render('ville/creer.html.twig', [
            'form' => $form,

        ]);
    }
}

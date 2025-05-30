<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TriSortiesForm extends AbstractType
{
    public function __construct(
        private TokenStorageInterface $security,
        private EntityManagerInterface $entityManager)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // Récupère l'utilisateur connecté

        $user = $this->security->getToken()->getUser();
        $campusUtilisateurId = $user->getCampus()->getId();
        dump($campusUtilisateurId);

        $campusUtilisateur = $this->entityManager->getRepository(Campus::class)->find($campusUtilisateurId);
        dump($campusUtilisateur);

        $builder
            ->add('nom', SearchType::class, [
                'label' => 'Le nom de la sortie contient',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom', // ou autre champ affichable
                'data' => $campusUtilisateur,
                'required' => true,
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text', // pour afficher le champ sous un seul input
                'required' => true,
                'label' => 'Entre',
                'mapped' => false, //Champs non relié à l'entité
            ])
            ->add('dateHeureFin', DateTimeType::class, [
                'widget' => 'single_text', // pour afficher le champ sous un seul input
                'required' => true,
                'label' => 'Et',
                'mapped' => false, //Champs non relié à l'entité
            ])
            ->add('organisateur', CheckboxType::class, [
                'label'    => "Sorties dont je suis l'organisateur/trice",
                'required' => false, // Cette case doit être cochée pour soumettre le formulaire
                'mapped'   => false, // Ce champ n'est pas lié à un champ d'entité
            ])
            ->add('inscrit', CheckboxType::class, [
                'label'    => "Sorties où je suis inscrit/e",
                'required' => false, // Cette case doit être cochée pour soumettre le formulaire
                'mapped'   => false, // Ce champ n'est pas lié à un champ d'entité
            ])
            ->add('pasInscrit', CheckboxType::class, [
                'label'    => "Sorties où je ne suis pas inscrit/e",
                'required' => false, // Cette case doit être cochée pour soumettre le formulaire
                'mapped'   => false, // Ce champ n'est pas lié à un champ d'entité
            ])
            ->add('sortieTerminee', CheckboxType::class, [
                'label'    => "Sorties terminées",
                'required' => false, // Cette case doit être cochée pour soumettre le formulaire
                'mapped'   => false, // Ce champ n'est pas lié à un champ d'entité
            ]);



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

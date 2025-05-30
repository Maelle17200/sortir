<?php

namespace App\Form;

use App\DTO\RechercheSortiesDTO;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RechercheSortiesForm extends AbstractType
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Récupérer l'utilisateur connecté
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user instanceof Participant)
        {
            // Récupérer le campus de l'utilisateur connecté
            $campus = $user->getCampus();
        }

        $builder
            ->add('nom', TextType::class, [
                'label' => 'nom',
                'required' => false,
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateHeureFin', DateTimeType::class, [
                'label' => 'Date de Fin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus',
                'required' => false,
                'data' => $campus,
            ])
            ->add('userOrganisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
                'mapped' => false,
            ])
            ->add('userInscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e",
                'required' => false,
                'mapped' => false,
            ])
            ->add('userPasInscrit', CheckboxType::class, [
                'label' => "Sorties dont je ne suis pas inscrit/e",
                'required' => false,
                'mapped' => false,
            ])
            ->add('sortieTerminee', CheckboxType::class, [
                'label' => "Sorties terminées",
                'required' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RechercheSortiesDTO::class,
        ]);
    }
}
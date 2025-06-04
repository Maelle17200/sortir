<?php

namespace App\Form;

use App\DTO\RechercheSortiesDTO;
use App\Entity\Campus;
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
                'label' => "L'intitulé de la sortie contient",
                'required' => false,
            ])
            ->add('dateHeureDebutRecherche', DateTimeType::class, [
                'label' => 'Entre le',
                'widget' => 'single_text',
                'required' => false,
                'empty_data' => '1971-01-01 00:00:00', // si champ non rempli, renvoi le 1e janvier 1900
                //'input' => 'datetime_immutable',
            ])
            ->add('dateHeureFinRecherche', DateTimeType::class, [
                'label' => 'Et le',
                'widget' => 'single_text',
                'required' => false,
                'empty_data' => '2038-01-01 00:00:00',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Sortie rattachée au campus de',
                'required' => true,
                'data' => $campus,
            ])
            ->add('tousCampus', CheckboxType::class, [
                'required' => false,
                'label' => 'Tous les campus',
            ])
            ->add('userOrganisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false,
            ])
            ->add('userInscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e",
                'required' => false,
            ])
            ->add('userPasInscrit', CheckboxType::class, [
                'label' => "Sorties dont je ne suis pas inscrit/e",
                'required' => false,
            ])
            ->add('sortiesTerminees', CheckboxType::class, [
                'label' => "Sorties terminées",
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RechercheSortiesDTO::class,
        ]);
    }
}
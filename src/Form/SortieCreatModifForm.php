<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SortieCreatModifForm extends AbstractType
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
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Début de la sortie',
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée en minutes'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => "Date limite d'inscription",
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [
                'label' => "Nombre maximum de participants",

            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => "Informations sur la sortie",
            ])
            ->add('lieu', EntityType::class, [
                'label' => "Lieu",
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('campus', EntityType::class, [
                'label' => "Campus",
                'class' => Campus::class,
                'choice_label' => 'nom',
                'disabled' => false,
                'data' => $campus,
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-secondary'],
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
                'attr' => ['class' => 'btn btn-secondary'],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

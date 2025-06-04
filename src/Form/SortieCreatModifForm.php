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

class SortieCreatModifForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('duree', IntegerType::class, [])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionMax', IntegerType::class, [])
            ->add('infosSortie', TextareaType::class, [])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
            ])
            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-secondary'],
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
                'attr' => ['class' => 'btn btn-secondary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ParticipantForm extends AbstractType
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $user = $this->tokenStorage->getToken()->getUser();
        $isAdmin = ($user->getRoles()[0] == 'ROLE_ADMIN');

        if($isAdmin){
            $builder->add('actif', CheckboxType::class, [
                'label' => 'Actif',
            ]);
        }
        if($isAdmin){
            $builder
                ->add('campus', EntityType::class, [
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                    'disabled' => false,
                    'label' => 'Campus',
                ]);
        }
        if(!$isAdmin){
            $builder
                ->add('campus', EntityType::class, [
                    'class' => Campus::class,
                    'choice_label' => 'nom',
                    'disabled' => true,
                    'label' => 'Campus',
                ]);
        }
        $builder
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\Image([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'L\'image ne peut pas dépasser 1 Mo.',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG uniquement.',
                    ])
                ],
                'label' => "Image de profil (facultatif)",
            ])
            ->add('password', PasswordType::class, [
                'required' => true, // Le mot de passe n'est pas requis lors de la modif
                'mapped' => true,
                'label' => 'mot de passe',
            ])
            ->add('password1', PasswordType::class, [
                'required' => true,
                'mapped' => false,
                'label' => 'Confirmation',
            ])
        ;}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
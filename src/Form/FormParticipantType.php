<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Utilisateur;
use ContainerN9gqLe0\getUtilisateurAuthenticatorService;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class FormParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'constraints' => [new Length([
                        'min' => 3,
                        'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => false,
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'constraints' => [new Length([
                    'min' => 3,
                    'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => false,
            ])

            ->add('telephone', TextType::class, [
                'label' => 'Téléphone :'])

            ->add('email', TextType::class, [
                'label' => 'Email personnel :',
                'constraints' => [new Length([
                    'min' => 3,
                    'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => false,
            ])


            ->add('site', EntityType::class, [
                'class' =>'App\Entity\Site',
                'label' => 'Site de rattachement :',
                "attr" => ['class' => "checkbox"]
            ]

            )
            ->add('my_file', FileType::class, [
                'mapped'=>false,
                    'label'=>'Mon avatar',
                    'required'=>false
                ]

            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Utilisateur;
use ContainerN9gqLe0\getUtilisateurAuthenticatorService;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
                'attr' => ['class' => 'input is-link'],
                'constraints' => [new Length([
                        'min' => 3,
                        'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => false,
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'attr' => ['class' => 'input is-link'],
                'constraints' => [new Length([
                    'min' => 3,
                    'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => false,
            ])

            ->add('telephone', TelType::class, [
                'attr' => ['class' => 'input is-link'],
                'label' => 'Téléphone :'])

            ->add('email', EmailType::class, [
                'label' => 'Email personnel :',
                'attr' => ['class' => 'input is-link'],
                'constraints' => [new Length([
                    'min' => 3,
                    'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => false,
            ])


            ->add('site', EntityType::class, [
                'class' =>'App\Entity\Site',
                'label' => 'Site de rattachement :',
                "attr" => ['class' => "select is-link"]
            ]

            )
            ->add('my_file', FileType::class, [
                'mapped'=>false,
                    'label'=>false,
                    'required'=>false,
                    "attr" => ['class' => "file-input"]
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

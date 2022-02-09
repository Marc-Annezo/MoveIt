<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
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
        /*    ->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Les mots de passe doivent être identiques',
                        'options' => ['attr' => ['class' => 'password-field']],
                        'required' => false,
                        'first_options'  => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Confirmation'],
])*/
            ->add('site', TextType::class, ['label' => 'Ville de rattachement :'])

            ->add('password',
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

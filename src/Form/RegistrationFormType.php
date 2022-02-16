<?php

namespace App\Form;

use App\Entity\Utilisateur;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'],
                'required'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ]),
                ],
            ])

            ->add('admin', CheckboxType::class, [
                'label'  => 'admin',
                'mapped' => false,
                'required'=>false,
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 999999,
                    ]),
                ],
            ])

            ->add('my_csv', FileType::class, [
                    'mapped'=>false,
                    'label'=>false,
                    'required'=>false,
                    "attr" => ['class' => "file-input",
                        'type'=>'file'

                    ],

                ]

            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

//{{ form_row(creerSortie.nom, {
//                'label_attr': {'class': ' text-primary'},
//                'attr' : {'class':'input is-link',
//                    'placeholder':'Quelle sortie ?',
//                    'type':'text',
//                    'aria-describedby':"inputGroup-sizing-sm"
//                },
//            }) }}

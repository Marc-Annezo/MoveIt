<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label_attr' => ['class' => 'text-primary'],
                'attr' => [
                    'class' => 'input is-link is-rounded',
                    'placeholder' => 'Quelle sortie ?',
                    'type' => 'text',
                    'aria-describedby' => "inputGroup-sizing-sm"
                ]

            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label_attr' => ['class' => 'text-primary'],
                'widget'=>'single_text',
                'data' => new \DateTime('now + 1 day'),
                'attr' => [
                    'class' => 'input is-link is-rounded',
                    'type'=>'date',
                ]


            ])


            ->add('dateHeureFin', DateTimeType::class, [
                'label_attr' => ['class' => 'text-primary'],
                'widget'=>'single_text',
                'data' => new \DateTime('now + 2 day'),
                'attr' => [
                    'class' => 'input is-link is-rounded',
                    'type'=>'date',
                ]
            ])

            ->add('dateLimiteInscription',DateTimeType::class, [
                'label_attr' => ['class' => 'text-primary'],
                'widget'=>'single_text',
                'data' => new \DateTime('now'),
                'attr' => [
                    'class' => 'input is-link is-rounded',
                    'type'=>'date',
                ]
            ])

            ->add('nbInscriptionsMax',NumberType::class, [
                'label_attr' => ['class' => 'text-primary'],
                'attr' => [
                    'class' => 'input is-link is-rounded',
                    'placeholder' => '',
                    'type' => 'text',
                    'aria-describedby' => "inputGroup-sizing-sm"
                ]
            ])
            ->add('InfosSortie', TextType::class, [
                'label_attr' => ['class' => 'text-primary'],
                'attr' => [
                    'class' => 'input is-link is-rounded',
                    'placeholder' => 'Quelle sortie ?',
                    'type' => 'text',
                    'aria-describedby' => "inputGroup-sizing-sm"
                ]

            ])

//            ->add('lieu', EntityType::class, [
//                'class' => Lieu::class,
//                'label_attr' => ['class' => 'text-primary'],
//                'attr' => [
//                    'id' => 'selectLieuSymfo',
//                    'class' => 'input is-link',
//                    'placeholder' => '',
//                    'type' => 'text',
//                    'aria-describedby' => "inputGroup-sizing-sm"
//                ]
//
//            ])
            ->add('creer', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'button is-block is-info is-normal is-fullwidth'],
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier la sortie',
                'attr' => ['class' => 'button is-block is-info is-normal is-fullwidth'],
            ]);


        //           ->add('site')
//          ->add('ville', EntityType::class,
//            ['class' => Ville::class,
//              'choice_label' => 'nom',

//]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

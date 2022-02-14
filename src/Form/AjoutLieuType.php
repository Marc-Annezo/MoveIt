<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label_attr'=> ['class'=> ' text-primary'],
                'attr' => ['class'=>'input is-link is-rounded',
                    'placeholder'=>'Que voulez vous faire ?',
                    'type'=>'text',
                    'aria-describedby'=>"inputGroup-sizing-sm"]
                ]
            )

            ->add('rue',TextType::class, [
                    'label_attr'=> ['class'=> ' text-primary'],
                    'attr' => ['class'=>'input is-link is-rounded',
                        'placeholder'=>'Où ça ?',
                        'type'=>'text',
                        'aria-describedby'=>"inputGroup-sizing-sm"]
                ]
            )

            ->add('latitude',NumberType::class, [
                    'label_attr'=> ['class'=> ' text-primary'],
                    'attr' => ['class'=>'input is-link is-rounded',
                        'placeholder'=>'',
                        'type'=>'text',
                        'aria-describedby'=>"inputGroup-sizing-sm"]
                ])

            ->add('longitude', NumberType::class, [
                    'label_attr'=> ['class'=> ' text-primary'],
                    'attr' => ['class'=>'input is-link is-rounded',
                        'placeholder'=>'',
                        'type'=>'text',
                        'aria-describedby'=>"inputGroup-sizing-sm"]
            ])

            ->add('idVille', EntityType::class, [
                'class' => Ville::class,
                'label' => 'Ville',
                'label_attr'=> ['class'=> ' text-primary'],
                'attr' => ['class'=>'input is-link is-rounded',
                    'placeholder'=>'',
                    'type'=>'date',
                    'aria-describedby'=>"inputGroup-sizing-sm"]
            ])

            ->add('valider', SubmitType::class, [
                'label' => 'Valider',
                'attr' => ['class' => 'button is-block is-info is-normal '],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}

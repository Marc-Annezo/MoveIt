<?php

namespace App\Form;

use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class FormVilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => ' ',

                'constraints' => [new Length([
                    'min' => 3,
                    'minMessage' => 'Doit comporter au moins 2 caractères'])
                ],
                'required' => true
            ])
            ->add('CodePostal', TextType::class, [
                'label' => ' ',
            'constraints' => [new Length([
                'min' => 5,
                'minMessage' => 'Doit comporter 5 chiffres',
                'max' => 5,
                'maxMessage' => 'Doit comporter 5 chiffres'])
            ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ville::class,
        ]);
    }
}

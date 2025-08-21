<?php

namespace App\Form;

use App\Entity\Lieu;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'attr' => [
                    'readonly' => false,
                    'placeholder' => 'Nom du lieu (modifiable après recherche)'
                ],
            ])
            ->add('rue', null, [
                'attr' => [
                    'readonly' => true,
                    'placeholder' => 'Adresse remplie automatiquement'
                ]
            ])
            ->add('latitude', null, [
                'attr' => [
                    'readonly' => true,
                    'placeholder' => 'Latitude remplie automatiquement'
                ]
            ])
            ->add('longitude', null, [
                'attr' => [
                    'readonly' => true,
                    'placeholder' => 'Longitude remplie automatiquement'
                ]
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

<?php

namespace App\Form;

use App\Entity\GroupePrive;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupePriveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
//            ->add('dateCreation', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('createur', EntityType::class, [
//                'class' => User::class,
//                'choice_label' => 'id',
//            ])
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GroupePrive::class,
        ]);
    }
}

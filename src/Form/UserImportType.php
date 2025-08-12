<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class UserImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('csv_file', FileType::class, [
            'label' => 'Fichier CSV',
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new File([
                    'mimeTypes' => ['text/csv', 'text/plain', 'application/vnd.ms-excel'],
                    'mimeTypesMessage' => 'Veuillez uploader un fichier CSV valide.',
                ])
            ]
        ]);
    }

}
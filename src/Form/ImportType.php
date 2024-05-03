<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', HiddenType::class, [
                'data' => 'member',
            ])
            ->add('file', FileType::class, [
                'label' => 'app.fields.import.file',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File(
                        extensions: [
                            'csv' => ['application/vnd.ms-excel', 'text/csv', 'text/plain'],
                        ],
                        extensionsMessage: 'member.import.invalid_file_type'
                    ),
                ],
                'attr' => ['accept' => '.csv, text/csv'],
            ])
        ;
    }
}

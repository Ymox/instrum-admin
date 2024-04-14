<?php

namespace App\Form;

use App\Entity\NoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'label_format' => 'app.fields.noteType.%name%.label',
            ])
            ->add('icon', null, [
                'required' => true,
                'label_format' => 'app.fields.noteType.%name%.label',
            ])
            ->add('description', null, [
                'required' => true,
                'label_format' => 'app.fields.noteType.%name%.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NoteType::class,
        ]);
    }
}

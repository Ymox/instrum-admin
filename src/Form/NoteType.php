<?php

namespace App\Form;

use App\Entity\Member;
use App\Entity\Note;
use App\Entity\NoteType as EntityNoteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => EntityNoteType::class,
                'required' => false,
                'choice_label' => 'name',
                'placeholder' => 'app.fields.note.type.placeholder',
                'choice_label' => 'name',
                'choice_attr'  => function (EntityNoteType $note) {
                    return [
                        'title' => $note->getDescription(),
                    ];
                },
            ])
            ->add('content')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}

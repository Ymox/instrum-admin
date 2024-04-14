<?php

namespace App\Form;

use App\Entity\NoteType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;

class NoteTypeHidderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EntityType::class, [
                'class' => NoteType::class,
                'expanded' => true,
                'multiple' => true,
                'label' => 'app.fields.member.informations.filter',
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],
                'label_html' => true,
                'choice_label' => function(NoteType $type): string {
                    return '<i class="fas fa-' . $type->getIcon() . '"></i> ' . $type->getName();
                },
                'choice_attr' => function(NoteType $type) {
                    return ['data-hide' => $type->getName()];
                },
            ]);
    }
}

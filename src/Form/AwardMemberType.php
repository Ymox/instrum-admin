<?php

namespace App\Form;

use App\Entity\Award;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AwardMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('member', null, [
                'label_format' => 'app.fields.award.%name%.label',
            ])
            ->add('year', null, [
                'label_format' => 'app.fields.award.%name%.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Award::class,
        ]);
    }
}

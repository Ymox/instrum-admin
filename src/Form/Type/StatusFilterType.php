<?php

namespace App\Form\Type;

use App\Entity\Status;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('on', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Status::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => function (Status $status) {
                    return str_repeat('    ', $status->getLvl()) . $status->getName();
                },
                'translation_domain' => false,
                'choice_name' => 'id',
                'choice_value' => 'name',
                'choice_attr' => function (Status $status) {
                    return [
                        'data-lft' => $status->getLft(),
                        'data-rgt' => $status->getRgt(),
                        'class' => 'positive',
                    ];
                }
            ])
            ->add('off', EntityType::class, [
                'required' => false,
                'label' => false,
                'class' => Status::class,
                'expanded' => true,
                'multiple' => true,
                'choice_label' => function (Status $status) {
                    return str_repeat('    ', $status->getLvl()) . $status->getName();
                },
                'translation_domain' => false,
                'choice_name' => 'id',
                'choice_value' => 'name',
                'choice_attr' => function (Status $status) {
                    return [
                        'data-lft' => $status->getLft(),
                        'data-rgt' => $status->getRgt(),
                        'class' => 'negative',
                    ];
                }
            ])
        ;
    }
}
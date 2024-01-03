<?php

namespace App\Form;

use App\Entity\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label_format' => 'app.fields.status.%name%.label',
            ])
            ->add('parent', null, [
                'label_format' => 'app.fields.status.%name%.label',
                'placeholder' => 'app.fields.status.parent.placeholder',
                'choice_label' => function (Status $status) {
                    $result = $status->getName();
                    $parent = $status;
                    $tree = [];
                    while ($parent = $parent->getParent()) {
                        $tree[] = $parent->getName();
                    }

                    if (empty($tree)) {
                        return $result;
                    }

                    return $result . ' (< ' . implode(' < ', $tree) . ')';
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Status::class,
        ]);
    }
}

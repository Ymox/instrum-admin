<?php

namespace App\Form;

use App\Entity\Provision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberProvisionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', null, [
                'label_format' => 'app.fields.provision.%name%.label',
            ])
            ->add('details', null, [
                'label_format' => 'app.fields.provision.%name%.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Provision::class,
        ]);
    }
}

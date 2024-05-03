<?php

namespace App\Form\Embeddable;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressImportType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', null, [
                'label_format' => 'app.fields.member.address.%name%.label',
            ])
            ->add('locality', null, [
                'label_format' => 'app.fields.member.address.%name%.label',
            ])
            ->add('zip', null, [
                'label_format' => 'app.fields.member.address.%name%.label',
            ])
        ;
    }
}

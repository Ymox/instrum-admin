<?php

namespace App\Form\Embeddable;

use App\Entity\Embeddable\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    private $urlGenerator;

    public function __construct(\Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}

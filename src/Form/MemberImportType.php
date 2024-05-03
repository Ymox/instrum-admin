<?php

namespace App\Form;

use App\Entity\Member;
use App\Form\Embeddable\AddressImportType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', ChoiceType::class, [
                'required' => false,
                'choices' => array_combine(Member::$TITLES, Member::$TITLES),
                'label_format' => 'app.fields.member.%name%.label',
                'placeholder' => 'app.fields.member.title.placeholder',
                'choice_translation_domain' => false,
            ])
            ->add('lastname', null, [
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('firstname', null, [
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('address', AddressImportType::class, [
                'label_format' => 'app.fields.member.%name%.label',
                'block_prefix' => 'member_import',
            ])
            ->add('mobile', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
                'attr' => [
                    'placeholder' => 'app.fields.member.mobile.placeholder',
                    'data-mask' => '+9999/999.99.99'
                ],
            ])
            ->add('phone', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
                'attr' => [
                    'placeholder' => 'app.fields.member.phone.placeholder',
                    'data-mask' => '+9999/999.99.99'
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('birthday', BirthdayType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label_format' => 'app.fields.member.%name%.label',
                'input' => 'string',
                'input_format' => 'd.m.Y',
            ])
            ->add('scmv', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('scmvCorrection', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
                'help' => 'app.fields.member.scmvCorrection.help',
            ])
            ->add('band', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('cat', null, [
                'label_format' => 'app.fields.member.%name%.label',
            ])
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'member';
    }
}

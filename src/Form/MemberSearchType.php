<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Status;
use App\Form\Type\StatusFilterType;
use App\Form\Embeddable\AddressType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberSearchType extends AbstractType
{
    private $urlGenerator;

    public function __construct(\Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('lastname', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('firstname', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('address', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('mobile', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('phone', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('email', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('birthday', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('scmv', IntegerType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('band', IntegerType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('cat', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'expanded' => true,
                'multiple' => true,
                'choice_name' => 'id',
                'choice_value' => 'name',
                'translation_domain' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('informations', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('relateds', SearchType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
                'attr' => [
                    'data-uri' => $this->urlGenerator->generate('member_new'),
                ]
            ])
            ->add('statuses', StatusFilterType::class, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('levels', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'q';
    }
}

<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Subcategory;
use App\Entity\Instrument;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstrumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', null, [
                'empty_data' => '',
                'label_format' => 'app.fields.instrument.%name%.label',
            ])
            ->add('brand', null, [
                'empty_data' => '',
                'label_format' => 'app.fields.instrument.%name%.label',
            ])
            ->add('usable', null, [
                'required' => false,
                'label_format' => 'app.fields.instrument.%name%.label',
            ])
            ->add('property', null, [
                'label_format' => 'app.fields.instrument.%name%.label',
            ])
            ->add('recipient', null, [
                'label_format' => 'app.fields.instrument.%name%.label',
            ])
            ->add('information', null, [
                'label_format' => 'app.fields.instrument.%name%.label',
            ])
            ->add('cat', null, [
                'label_format' => 'app.fields.instrument.%name%.label',
                'placeholder'  => 'app.fields.instrument.cat.placeholder',
            ])
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $this->addSubcategories($event->getForm(), $event->getData()->getCat());
                }
            );

        $builder
            ->get('cat')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addSubcategories($form->getParent(), $form->getData());
                }
            );
    }

    private function addSubcategories(FormInterface $form, ?Category $category)
    {
        $form
            ->add('subcat', EntityType::class, [
                'required' => false,
                'class'    => Subcategory::class,
                'label_format' => 'app.fields.instrument.subcat.label',
                'placeholder' => 'app.fields.instrument.subcat.placeholder',
                'query_builder' => function (EntityRepository $repo) use ($category) {
                    $qb = $repo->createQueryBuilder('sc');
                    $qb ->where($qb->expr()->eq('sc.cat', ':category'))
                        ->setParameter(':category', $category);

                    return $qb;
                },
                'attr' => [
                    'data-dynamic-options' => 'cat'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instrument::class,
        ]);
    }
}

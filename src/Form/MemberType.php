<?php

namespace App\Form;

use App\Entity\Instrument;
use App\Entity\Member;
use App\Entity\Status;
use App\Form\Embeddable\AddressType;
use App\Repository\StatusRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    private $urlGenerator;

    public function __construct(\Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

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
            ->add('address', AddressType::class, [
                'label_format' => 'app.fields.member.%name%.label',
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
            ->add('relateds', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
                'attr' => [
                    'data-uri' => $this->urlGenerator->generate('member_new'),
                ]
            ])
            ->add('statuses', null, [
                'label_format' => 'app.fields.member.%name%.label',
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
                'choice_attr' => function (Status $status) {
                    return [
                        'data-lft' => $status->getLft(),
                        'data-rgt' => $status->getRgt(),
                    ];
                },
                'query_builder' => function (StatusRepository $repo) {
                    $qb = $repo->createQueryBuilder('s');
                    return $qb->orderBy('lft', 'ASC');
                },
            ])
            ->add('levels', null, [
                'required' => false,
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('instruments', EntityType::class, [
                'required' => false,
                'class' => Instrument::class,
                'multiple' => true,
                'label_format' => 'app.fields.member.%name%.label',
                'query_builder' => function (\App\Repository\InstrumentRepository $repo) {
                    $qb = $repo->createQueryBuilder('i');
                    $qb ->where($qb->expr()->isNull('i.recipient'))
                        ->andWhere($qb->expr()->eq('i.usable', $qb->expr()->literal(true)))
                        ->innerJoin('i.cat', 'c')->addSelect('c')
                        ->leftJoin('i.subcat', 's')->addSelect('s')
                        ->orderBy('c.id', 'ASC')
                        ->addOrderBy('s.id', 'ASC');

                    return $qb;
                },
                'choice_label' => function (\App\Entity\Instrument $instrument) {
                    return $instrument . ' ' . $instrument->getBrand() . ' ' . $instrument->getNumber();
                },
                'attr' => [
                    'class' => 'searchable',
                ],
            ])
        ;

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $this->addInstruments($event->getForm(), $event->getData());
                    $this->addStatuses($event->getForm(), $event->getData()->getTitle());
                }
            );

        $builder
            ->get('instruments')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $this->addInstruments($event->getForm()->getParent(), $event->getForm()->getData());
                }
            );

        $builder
            ->get('title')
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $this->addStatuses($event->getForm()->getParent(), $event->getForm()->getData());
                }
            );
    }

    private function addInstruments(FormInterface $form, ?Member $member)
    {
        $form
            ->add('instruments', EntityType::class, [
                'required' => false,
                'class' => Instrument::class,
                'multiple' => true,
                'label_format' => 'app.fields.member.%name%.label',
                'query_builder' => function (\App\Repository\InstrumentRepository $repo) use ($member) {
                    $qb = $repo->createQueryBuilder('i');
                    if ($member->getId()) {
                        $qb ->where($qb->expr()->orX(
                                $qb->expr()->isNull('i.recipient'),
                                $qb->expr()->eq('i.recipient', ':recipient')
                            ))
                            ->setParameter(':recipient', $member);
                    } else {
                        $qb->where($qb->expr()->isNull('i.recipient'));
                    }
                    $qb ->andWhere($qb->expr()->eq('i.usable', $qb->expr()->literal(true)))
                        ->innerJoin('i.cat', 'c')->addSelect('c')
                        ->leftJoin('i.subcat', 's')->addSelect('s')
                        ->orderBy('c.id', 'ASC');

                    return $qb;
                },
                'choice_label' => function (\App\Entity\Instrument $instrument) {
                    return $instrument . ' ' . $instrument->getBrand() . ' ' . $instrument->getNumber();
                },
            ]);
    }
    
    private function addStatuses(FormInterface $form, ?string $title)
    {
        $form
            ->add('statuses', EntityType::class, [
                'required' => false,
                'multiple' => true,
                'class' => Status::class,
                'label_format' => 'app.fields.member.%name%.label',
                'query_builder' => function (\App\Repository\StatusRepository $repo) use ($title) {
                    $qb = $repo->createQueryBuilder('s');
                    if (!$title) {
                        $qb ->join('s.root', 'r', Join::WITH, $qb->expr()->gt('r.lft', 1));
                    }

                    return $qb;
                },
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
                'choice_attr' => function (Status $status) {
                    return [
                        'data-lft' => $status->getLft(),
                        'data-rgt' => $status->getRgt(),
                    ];
                },
                'attr' => [
                    'data-dynamic-options' => 'title',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\AwardType;
use App\Entity\Status;
use App\Repository\StatusRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AwardTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label_format' => 'app.fields.awardType.%name%.label',
            ])
            ->add('years', null, [
                'label_format' => 'app.fields.awardType.%name%.label',
            ])
            ->add('column', ChoiceType::class, [
                'choices' => array_combine(AwardType::$COLUMNS, AwardType::$COLUMNS),
                'label_format' => 'app.fields.awardType.%name%.label',
                'choice_label' => function ($choice, $key, $value) {
                    return 'app.fields.awardType.column.' . $value;
                }
            ])
            ->add('statusToAward', null, [
                'label_format' => 'app.fields.awardType.%name%.label',
            ])
            ->add('eligibleStatuses', EntityType::class, [
                'class' => Status::class,
                'multiple' => true,
                'query_builder' => function (StatusRepository $repo) {
                    $qb = $repo->createQueryBuilder('s');
                    $qb ->join('s.root', 'r', Join::WITH, $qb->expr()->lte('r.lft', 1));
                    
                    return $qb;
                },
                'choice_label' => function (Status $status) {
                    return str_repeat('    ', $status->getLvl()) . $status->getName();
                },
                'label_format' => 'app.fields.awardType.%name%.label',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AwardType::class,
        ]);
    }
}

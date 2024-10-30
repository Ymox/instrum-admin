<?php

namespace App\Controller;

use App\Entity\Award;
use App\Entity\AwardType;
use App\Entity\Member;
use App\Form\AwardMemberType;
use App\Form\MemberAwardType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DecernController extends AbstractController
{
    #[Route("/member/{member}/decern", name: "member_decern", methods: ["GET", "POST"])]
    #[Route("/award-type/{awardType}/decern", name: "award_type_decern", methods: ["GET", "POST"])]
    public function decern(
        Request $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        string $_route,
        #[MapEntity(id: 'member')] ?Member $member,
        #[MapEntity(id: 'awardType')] ?AwardType $awardType
    ): Response
    {
        if ($member) {
            $source = 'member';
            $fieldName = 'awards';
            $entryType = MemberAwardType::class;
            $routeParams = ['member' => $member->getId()];
        } else {
            $source = 'award_type';
            $fieldName = 'members';
            $entryType = AwardMemberType::class;
            $routeParams = ['awardType' => $awardType->getId()];
        }
        $award = (new Award())
            ->setYear((int) date('Y'));

        $form = $formFactory
            ->createNamedBuilder('decern', FormType::class, [$fieldName => [$award]], [
                'action' => $this->generateUrl($_route, $routeParams),
            ])
            ->add($fieldName, CollectionType::class, [
                'entry_type' => $entryType,
                'by_reference' => true,
                'label' => 'app.fields.member.awards.label',
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => false,
                ],
                'prototype_data' => $award,
            ])
            ->getForm();
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()[$fieldName] as $award) {
                $award
                    ->setMember($award->getMember() ?? $member)
                    ->setAwardType($award->getAwardType() ?? $awardType)
                ;
                $entityManager->persist($award);
            }
            $entityManager->flush();

            $routeParams = array_combine(['id'], $routeParams);
            return $this->redirectToRoute($source . '_show', $routeParams, Response::HTTP_SEE_OTHER);
        }

        return $this->render('award/new.html.twig', [
            'source' => $source,
            'member' => $member,
            'awardType' => $awardType,
            'form' => $form,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Provision;
use App\Entity\Stock;
use App\Form\MemberProvisionType;
use App\Form\StockProvisionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProvideController extends AbstractController
{
    #[Route("/member/{member}/provide", name: "member_provide", methods: ["GET", "POST"])]
    #[Route("/stock/{stock}/provide", name: "stock_provide", methods: ["GET", "POST"])]
    public function provide(
        Request $request,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        string $_route,
        ?Member $member,
        ?Stock $stock
    ): Response
    {
        $formData['provisions'] = [];
        if ($member) {
            $source = 'member';
            $fieldName = 'stocks';
            $entryType = MemberProvisionType::class;
            $routeParams = ['member' => $member->getId()];
        } else {
            $source = 'stock';
            $fieldName = 'members';
            $entryType = StockProvisionType::class;
            $routeParams = ['stock' => $stock->getId()];
        }
        $provision = new Provision();

        $form = $formFactory
            ->createNamedBuilder('provision', FormType::class, [$fieldName => [$provision]], [
                'action' => $this->generateUrl($_route, $routeParams),
            ])
            ->add($fieldName, CollectionType::class, [
                'entry_type' => $entryType,
                'by_reference' => true,
                'label' => 'app.fields.' . $source . '.provisions.label',
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => false,
                ],
            ])
            ->getForm();
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()[$fieldName] as $provision) {
                $provision
                    ->setMember($provision->getMember() ?? $member)
                    ->setStock($provision->getStock() ?? $stock)
                ;
                $entityManager->persist($provision);
            }
            $entityManager->flush();

            $routeParams = array_combine(['id'], $routeParams);
            return $this->redirectToRoute($source . '_show', $routeParams, Response::HTTP_SEE_OTHER);
        }

        return $this->render('provision/new.html.twig', [
            'source' => $source,
            'member' => $member,
            'stock' => $stock,
            'form' => $form,
        ]);
    }
}

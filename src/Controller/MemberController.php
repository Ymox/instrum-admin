<?php

namespace App\Controller;

use App\Entity\Member;
use App\Form\MemberAwardType;
use App\Form\MemberSearchType;
use App\Form\MemberType;
use App\Form\NoteTypeHidderType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route(name: "member_")]
class MemberController extends AbstractController
{
    #[Route("/person/{_format}", name: "index", methods: ["GET"], requirements: ["_format" => "^(?![\s\S])|html|csv"])]
    public function index(Request $request, EntityManagerInterface $entityManager, string $_format = 'html'): Response
    {
        $searchForm = $this->createForm(MemberSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
        $searchForm->handleRequest($request);
        $query = $searchForm->getData();

        $members = $entityManager
            ->getRepository(Member::class)
            ->searchBy(
                $query ?? [],
                [
                    $query['field'] ?? 'm.id' => $query['direction'] ?? 'asc',
                ]
            );

        $response = null;
        if ($_format != 'html') {
            $response = new Response();
            $response->headers->set('Content-Type', 'text/csv; charset="utf-8"; header=present');
            $response->headers->set('Content-Disposition', HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                'export.csv'
            ));
        }

        return $this->render(
            'member/index.' . $_format . '.twig',
            [
                'searchForm' => $searchForm,
                'members' => $members
            ],
            $response
        );
    }

    #[Route("/person/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $member = new Member();
        $formParameters = [];
        if ($request->isXmlHttpRequest()) {
            $formParameters['action'] = $this->generateUrl('member_new');
        }
        $form = $this->createForm(MemberType::class, $member, $formParameters);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($member);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'value' => $member->getId(),
                    'firstname' => $member->getFirstName(),
                    'lastname' => $member->getLastName(),
                    'text' => (string)$member,
                ]);
            }

            return $this->redirectToRoute('member_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/new.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route("/person/{id}", name: "show", methods: ["GET"], requirements: ["id" => "\d+"])]
    public function show(#[MapEntity] Member $member): Response
    {
        $form = $this->createForm(NoteTypeHidderType::class);
        return $this->render('member/show.html.twig', [
            'noteTypes' => $form,
            'member' => $member,
        ]);
    }

    #[Route("/person/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, #[MapEntity] Member $member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('member_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/edit.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route("/person/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, #[MapEntity] Member $member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$member->getId(), $request->request->get('_token'))) {
            $entityManager->remove($member);
            $entityManager->flush();
        }

        return $this->redirectToRoute('member_index', status: Response::HTTP_SEE_OTHER);
    }

    #[Route("/students", name: "students", methods: ["GET"])]
    public function students(Request $request, EntityManagerInterface $entityManager): Response
    {
        $members = $entityManager
            ->getRepository(Member::class)
            ->searchBy(
                ['statuses' => ['on' => new ArrayCollection([13]), 'off' => new ArrayCollection()]],
                [
                    $request->query->get('field', 'm.id') => $request->query->get('direction', 'asc'),
                ]
            );

        return $this->render('member/students.html.twig', [
            'members' => $members,
        ]);
    }

    #[Route("/members/{_format}", name: "members", methods: ["GET"], requirements: ["_format" => "^(?![\s\S])|html|csv"])]
    public function members(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchForm = $this->createForm(MemberSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
        $searchForm->handleRequest($request);
        $query = $searchForm->getData();

        $members = $entityManager
            ->getRepository(Member::class)
            ->searchBy(
                $query ?? ['statuses' => ['on' => new ArrayCollection([1]), 'off' => new ArrayCollection()]],
                [
                    $request->query->get('field', 'm.id') => $request->query->get('direction', 'asc'),
                ]
            );

        return $this->render('member/index.html.twig', [
            'members' => $members,
            'searchForm' => $searchForm,
        ]);
    }

    #[Route("/members/contributing/{_format}", name: "members", methods: ["GET"], requirements: ["_format" => "^(?![\s\S])|html|csv"])]
    public function contributing(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchForm = $this->createForm(MemberSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
        $searchForm->handleRequest($request);
        $query = $searchForm->getData();

        $members = $entityManager
            ->getRepository(Member::class)
            ->searchBy(
                $query ?? ['statuses' => ['on' => new ArrayCollection([1]), 'off' => new ArrayCollection([14])]],
                [
                    $request->query->get('field', 'm.id') => $request->query->get('direction', 'asc'),
                ]
            );

        return $this->render('member/index.html.twig', [
            'members' => $members,
            'searchForm' => $searchForm,
        ]);
    }

    /**
     * Allows to handle duplicates in database
     */
    #[Route("/person/duplicates/{master}/{duplicate}", name: "duplicates", methods: ["GET", "POST"])]
    public function duplicates(Request $request, Member $master, Member $duplicate, EntityManagerInterface $em)
    {
        $masterForm = $this->createForm(MemberType::class, $master, [
            'validation_groups' => ['duplicate']
        ]);
        $masterForm
            ->add('instruments', null, [
                'required' => false,
                'class' => \App\Entity\Instrument::class,
                'multiple' => true,
                'label_format' => 'app.fields.member.%name%.label',
                'query_builder' => function (\App\Repository\InstrumentRepository $repo) use ($master, $duplicate) {
                    $qb = $repo->createQueryBuilder('i');
                    $qb ->where($qb->expr()->orX(
                            $qb->expr()->isNull('i.recipient'),
                            $qb->expr()->in('i.recipient', ':recipients'),
                        ))
                        ->andWhere($qb->expr()->eq('i.usable', $qb->expr()->literal(true)))
                        ->innerJoin('i.cat', 'c')->addSelect('c')
                        ->leftJoin('i.subcat', 's')->addSelect('s')
                        ->orderBy('c.id', 'ASC')
                        ->addOrderBy('s.id', 'ASC')
                        ->setParameter(':recipients', [$master, $duplicate]);

                    return $qb;
                },
                'choice_label' => function (\App\Entity\Instrument $instrument) {
                    return $instrument . ' ' . $instrument->getBrand() . ' ' . $instrument->getNumber();
                },
                'attr' => [
                    'class' => 'searchable',
                ],
            ])
            ->add('relations', null, [
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('awards', CollectionType::class, [
                'label_format' => 'app.fields.member.%name%.label',
                'entry_type' => MemberAwardType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
        $masterForm->handleRequest($request);

        if ($masterForm->isSubmitted() && $masterForm->isValid()) {
            $em->flush();
            $em->remove($duplicate);
            $em->flush();
            $this->addFlash(
                'success',
                new TranslatableMessage(
                    'app.flash.success.duplicates.member'
                )
            );
            return $this->redirectToRoute('member_show', ['id' => $master->getId()]);
        }

        $duplicateForm = $this->createForm(MemberType::class, $duplicate);
        $duplicateForm
            ->add('instruments', null, [
                'required' => false,
                'class' => \App\Entity\Instrument::class,
                'multiple' => true,
                'label_format' => 'app.fields.member.%name%.label',
                'query_builder' => function (\App\Repository\InstrumentRepository $repo) use ($master, $duplicate) {
                    $qb = $repo->createQueryBuilder('i');
                    $qb ->where($qb->expr()->orX(
                            $qb->expr()->isNull('i.recipient'),
                            $qb->expr()->in('i.recipient', ':recipients'),
                        ))
                        ->andWhere($qb->expr()->eq('i.usable', $qb->expr()->literal(true)))
                        ->innerJoin('i.cat', 'c')->addSelect('c')
                        ->leftJoin('i.subcat', 's')->addSelect('s')
                        ->orderBy('c.id', 'ASC')
                        ->addOrderBy('s.id', 'ASC')
                        ->setParameter(':recipients', [$master, $duplicate]);

                    return $qb;
                },
                'choice_label' => function (\App\Entity\Instrument $instrument) {
                    return $instrument . ' ' . $instrument->getBrand() . ' ' . $instrument->getNumber();
                },
                'attr' => [
                    'class' => 'searchable',
                ],
            ])
            ->add('relations', null, [
                'label_format' => 'app.fields.member.%name%.label',
            ])
            ->add('awards', CollectionType::class, [
                'label_format' => 'app.fields.member.%name%.label',
                'entry_type' => MemberAwardType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;

        return $this->render('member/duplicates.html.twig', [
            'member' => $master,
            'master_form' => $masterForm->createView(),
            'duplicate_form' => $duplicateForm->createView(),
        ]);
    }
}

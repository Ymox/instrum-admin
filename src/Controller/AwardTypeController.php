<?php

namespace App\Controller;

use App\Entity\Award;
use App\Entity\AwardType;
use App\Form\AwardTypeType;
use App\Repository\AwardTypeRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/award-type", name: "award_type_")]
class AwardTypeController extends AbstractController
{
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(AwardTypeRepository $awardTypeRepository): Response
    {
        return $this->render('award_type/index.html.twig', [
            'awardTypes' => $awardTypeRepository->findBy([], ['years' => 'ASC']),
        ]);
    }

    #[Route("/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $awardType = new AwardType();
        $form = $this->createForm(AwardTypeType::class, $awardType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($awardType);
            $entityManager->flush();

            return $this->redirectToRoute('award_type_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('award_type/new.html.twig', [
            'awardType' => $awardType,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(#[MapEntity] AwardType $awardType, MemberRepository $memberRepository): Response
    {
        return $this->render('award_type/show.html.twig', [
            'awardType' => $awardType,
            'notAwarded' => $memberRepository->findNotAwarded($awardType),
        ]);
    }

    #[Route("/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, #[MapEntity] AwardType $awardType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AwardTypeType::class, $awardType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('award_type_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('award_type/edit.html.twig', [
            'awardType' => $awardType,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, #[MapEntity] AwardType $awardType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$awardType->getId(), $request->request->get('_token'))) {
            $entityManager->remove($awardType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('award_type_index', status: Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Award;
use App\Entity\Member;
use App\Form\MemberAwardType;
use App\Repository\AwardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/member/{member}", name: "award_")]
class AwardController extends AbstractController
{
    #[Route("/award", name: "index", methods: ["GET"])]
    public function index(AwardRepository $awardRepository, #[MapEntity(id: 'member')] Member $member): Response
    {
        return $this->render('award/index.html.twig', [
            'member' => $member,
            'awards' => $awardRepository->findByMember($member),
        ]);
    }

    #[Route("/award/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[MapEntity(id: 'member')] Member $member): Response
    {
        $award = new Award();
        $form = $this->createForm(MemberAwardType::class, $award, [
            'action' => $this->generateUrl('award_new', ['member' => $member->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $award->setMember($member);
            $entityManager->persist($award);
            $entityManager->flush();

            return $this->redirectToRoute('award_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('award/new.html.twig', [
            'member' => $member,
            'award' => $award,
            'form' => $form,
        ]);
    }

    #[Route("/award/{awardType}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, #[MapEntity(id: ['member' => 'member', 'awardType' => 'awardType'])] Award $award, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MemberAwardType::class, $award);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('award_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('award/edit.html.twig', [
            'award' => $award,
            'form' => $form,
        ]);
    }

    #[Route("/award/{awardType}/delete", name: "delete", methods: ["POST"])]
    public function delete(Request $request, #[MapEntity(id: ['member' => 'member', 'awardType' => 'awardType'])] Award $award, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$award->getMember()->getId() . $award->getAwardType()->getId(), $request->request->get('_token'))) {
            $entityManager->remove($award);
            $entityManager->flush();
        }

        return $this->redirectToRoute('award_index', status: Response::HTTP_SEE_OTHER);
    }
}

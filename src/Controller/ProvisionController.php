<?php

namespace App\Controller;

use App\Entity\Provision;
use App\Entity\Member;
use App\Form\ProvisionType;
use App\Repository\ProvisionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/member/{member}", name: "provision_")]
class ProvisionController extends AbstractController
{
    #[Route("/inventory", name: "index", methods: ["GET"])]
    public function index(ProvisionRepository $provisionRepository, #[MapEntity(id: 'member')] Member $member): Response
    {
        return $this->render('provision/index.html.twig', [
            'member' => $member,
            'provisions' => $provisionRepository->findByMember($member),
        ]);
    }

    #[Route("/stock/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[MapEntity(id: 'member')] Member $member): Response
    {
        $provision = new Provision();
        $form = $this->createForm(ProvisionType::class, $provision, [
            'action' => $this->generateUrl('provision_new', ['member' => $member->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $provision->setMember($member);
            $entityManager->persist($provision);
            $entityManager->flush();

            return $this->redirectToRoute('provision_index', ['member' => $member->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('provision/new.html.twig', [
            'member' => $member,
            'provision' => $provision,
            'form' => $form,
        ]);
    }

    #[Route("/stock/{stock}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, Provision $provision, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProvisionType::class, $provision);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('provision_index', ['member' => $provision->getMember()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('provision/edit.html.twig', [
            'provision' => $provision,
            'form' => $form,
        ]);
    }

    #[Route("/stock/{stock}/delete", name: "delete", methods: ["POST"])]
    public function delete(Request $request, Provision $provision, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provision->getMember()->getId() . '-' . $provision->getStock()->getId(), $request->request->get('_token'))) {
            $entityManager->remove($provision);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provision_index', ['member' => $provision->getMember()->getId()], Response::HTTP_SEE_OTHER);
    }
}

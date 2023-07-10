<?php

namespace App\Controller;

use App\Entity\Status;
use App\Form\StatusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/status", name: "status_")]
class StatusController extends AbstractController
{
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $statuses = $entityManager
            ->getRepository(Status::class)
            ->findAll();

        return $this->render('status/index.html.twig', [
            'statuses' => $statuses,
        ]);
    }

    #[Route("/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = new Status();
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($status);
            $entityManager->flush();

            return $this->redirectToRoute('status_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('status/new.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route("/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, Status $status, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StatusType::class, $status);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('status_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('status/edit.html.twig', [
            'status' => $status,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, Status $status, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$status->getId(), $request->request->get('_token'))) {
            $entityManager->remove($status);
            $entityManager->flush();
        }

        return $this->redirectToRoute('status_index', status: Response::HTTP_SEE_OTHER);
    }
}

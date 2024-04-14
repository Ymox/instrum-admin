<?php

namespace App\Controller;

use App\Entity\NoteType;
use App\Form\NoteTypeType;
use App\Repository\NoteTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/note/type', name: 'note_type_')]
class NoteTypeController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(NoteTypeRepository $noteTypeRepository): Response
    {
        return $this->render('note_type/index.html.twig', [
            'noteTypes' => $noteTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $noteType = new NoteType();
        $form = $this->createForm(NoteTypeType::class, $noteType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($noteType);
            $entityManager->flush();

            return $this->redirectToRoute('note_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note_type/new.html.twig', [
            'noteType' => $noteType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(NoteType $noteType): Response
    {
        return $this->render('note_type/show.html.twig', [
            'noteType' => $noteType,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NoteType $noteType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NoteTypeType::class, $noteType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('note_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note_type/edit.html.twig', [
            'noteType' => $noteType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, NoteType $noteType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noteType->getName(), $request->request->get('_token'))) {
            $entityManager->remove($noteType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('note_type_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member/{member}/note', name: 'note_')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        return $this->render('note/index.html.twig', [
            'notes' => $noteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, Member $member, EntityManagerInterface $entityManager): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note, [
            'action' => $this->generateUrl('note_new', ['member' => $member->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setMember($member);
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('member_show', ['id' => $member->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note, Member $member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('member_show', ['id', $member->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Note $note, Member $member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('member_show', ['id', $member->getId()], Response::HTTP_SEE_OTHER);
    }
}

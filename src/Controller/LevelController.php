<?php

namespace App\Controller;

use App\Entity\Level;
use App\Form\LevelType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/level", name: "level_")]
class LevelController extends AbstractController
{
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $levels = $entityManager
            ->getRepository(Level::class)
            ->findAll();

        return $this->render('level/index.html.twig', [
            'levels' => $levels,
        ]);
    }

    #[Route("/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $level = new Level();
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($level);
            $entityManager->flush();

            return $this->redirectToRoute('level_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('level/new.html.twig', [
            'level' => $level,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(#[MapEntity] Level $level): Response
    {
        return $this->render('level/show.html.twig', [
            'level' => $level,
        ]);
    }

    #[Route("/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, #[MapEntity] Level $level, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LevelType::class, $level);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('level_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('level/edit.html.twig', [
            'level' => $level,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, #[MapEntity] Level $level, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$level->getId(), $request->request->get('_token'))) {
            $entityManager->remove($level);
            $entityManager->flush();
        }

        return $this->redirectToRoute('level_index', status: Response::HTTP_SEE_OTHER);
    }
}

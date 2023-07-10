<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Subcategory;
use App\Form\SubcategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/subcategory", name: "subcategory_")]
class SubcategoryController extends AbstractController
{
    #[Route("/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $subcategory = new Subcategory();
        $form = $this->createForm(SubcategoryType::class, $subcategory, [
            'action' => $this->generateUrl('subcategory_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subcategory);
            $entityManager->flush();

            return $this->redirectToRoute('category_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategory/new.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form,
        ]);
    }

    #[Route("/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, Subcategory $subcategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubcategoryType::class, $subcategory, [
            'action' => $this->generateUrl('subcategory_edit', ['id' => $subcategory->getId()])
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return new Response(null, Response::HTTP_SEE_OTHER);
        }

        return $this->render('subcategory/edit.html.twig', [
            'subcategory' => $subcategory,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, Subcategory $subcategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$subcategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subcategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index', status: Response::HTTP_SEE_OTHER);
    }
}

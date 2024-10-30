<?php

namespace App\Controller;

use App\Entity\Instrument;
use App\Entity\Member;
use App\Form\InstrumentAssignMemberType;
use App\Form\InstrumentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/instrument", name: "instrument_")]
class InstrumentController extends AbstractController
{
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $instruments = $entityManager
            ->getRepository(Instrument::class)
            ->searchBy(
                $request->query->all('q'),
                [
                    $request->query->get('field', 'i.id') => $request->query->get('direction', 'asc'),
                ]
            );

        return $this->render('instrument/index.html.twig', [
            'instruments' => $instruments,
        ]);
    }

    #[Route("/new", name: "new", methods: ["GET", "POST"])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $instrument = new Instrument();
        $form = $this->createForm(InstrumentType::class, $instrument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($instrument);
            $entityManager->flush();

            return $this->redirectToRoute('instrument_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('instrument/new.html.twig', [
            'instrument' => $instrument,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(#[MapEntity] Instrument $instrument): Response
    {
        return $this->render('instrument/show.html.twig', [
            'instrument' => $instrument,
        ]);
    }

    #[Route("/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(Request $request, #[MapEntity] Instrument $instrument, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstrumentType::class, $instrument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('instrument_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('instrument/edit.html.twig', [
            'instrument' => $instrument,
            'form' => $form,
        ]);
    }

    #[Route("/{id}/assign", name: "assign_member", methods: ["GET", "POST"])]
    public function assign(Request $request, #[MapEntity] Instrument $instrument, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstrumentAssignMemberType::class, $instrument);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if ($request->isXmlHTTPRequest()) {
                return new Response(null, Response::HTTP_NO_CONTENT);
            } else {
                return $this->redirectToRoute('instrument_index', status: Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('instrument/assign.html.twig', [
            'instrument' => $instrument,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, #[MapEntity] Instrument $instrument, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$instrument->getId(), $request->request->get('_token'))) {
            $entityManager->remove($instrument);
            $entityManager->flush();
        }

        return $this->redirectToRoute('instrument_index', status: Response::HTTP_SEE_OTHER);
    }
}

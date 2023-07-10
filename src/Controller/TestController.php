<?php

namespace App\Controller;

use App\Entity\Status;
use App\Form\Type\StatusFilterType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route("/test")]
class TestController extends AbstractController
{
    #[Route("/", name: "test")]
    public function test(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('statuses', StatusFilterType::class, [
                'required' => false,
            ]
        );
        $form->getForm()->handleRequest($request);

        dump($form->getData());

        return $this->render('test.html.twig', [
            'form' => $form->getForm(),
        ]);
    }
}
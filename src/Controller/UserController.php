<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route("/user", name: "user_")]
class UserController extends AbstractController
{
    #[Route("/", name: "index", methods: ["GET"])]
    public function index(UserRepository $userRepository, UserInterface $user): Response
    {
        $this->denyAccessUnlessAdmin($user);

        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route("/new", name: "new", methods: ["GET", "POST"])]
    public function new(
        Request $request,
        UserInterface $user,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessAdmin($user);

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'action' => $this->generateUrl('user_new')
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )
            ->setLastConnection(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(#[MapEntity] User $user, UserInterface $authenticatedUser): Response
    {
        $this->denyAccessUnlessAdmin($authenticatedUser);

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route("/{id}/edit", name: "edit", methods: ["GET", "POST"])]
    public function edit(
        Request $request,
        #[MapEntity] User $user,
        UserInterface $authenticatedUser,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessAdmin($authenticatedUser);

        $form = $this->createForm(ChangePasswordType::class, null, [
            'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();

            return $this->redirectToRoute('user_index', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route("/{id}/ask-reset-password", name: "ask_reset_password", methods: ["GET"])]
    public function askResetPassword(
        #[MapEntity] User $user,
        UserInterface $authenticatedUser,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessAdmin($authenticatedUser);

        $user
            ->setResetToken(
                md5((new \DateTime())->format('Y-m-d\TH:i:s.uP') . uniqid() . $user->getUsername())
            )
            ->setLastConnection(new \DateTime());
        $entityManager->flush();
        $this->addFlash('success', 'app.flash.success.reset_password.asked');

        return $this->redirectToRoute('user_index', status: Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}", name: "delete", methods: ["POST"])]
    public function delete(Request $request, #[MapEntity] User $user, UserInterface $authenticatedUser, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessAdmin($authenticatedUser);

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', status: Response::HTTP_SEE_OTHER);
    }

    private function denyAccessUnlessAdmin(UserInterface $user): void
    {
        if ($user->getUserIdentifier() !== 'admin') {
            throw new AccessDeniedException();
        }
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityController extends AbstractController
{
    #[Route("/login", name: "login")]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route("/reset-password/{token}", name: "reset_password", methods: ["GET", "POST"])]
    public function resetPassword(
        Request $request,
        string $token,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $repository = $entityManager->getRepository(User::class);
        if (!($user = $repository->findOneByResetToken($token))) {
            throw $this->createNotFoundException();
        }

        $formBuilder = $this->createFormBuilder();
        $formBuilder
            ->add('password', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, [
                'first_options' =>  [
                    'label' => 'app.fields.user.password.first.label',
                ],
                'second_options' =>  [
                    'label' => 'app.fields.user.password.second.label',
                ],
                'type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'user.password.empty',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'user.password.too_short',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setPassword($userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                ))
                ->setResetToken(null);
            $entityManager->flush();

            $this->addFlash(
                'success',
                new TranslatableMessage(
                    'app.flash.success.reset_password.done'
                )
            );
            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/logout", name: "logout")]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall.
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileFormType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile', methods: ['GET'])]
    public function profile(): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash(
                'warning',
                'Connectez-vous pour consulter vos informations personnelles'
            );

            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        return $this->render('pages/user/profile.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/profile/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash(
                'warning',
                'Connectez-vous pour modifier vos informations personnelles'
            );

            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserProfileFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Vos informations personnelles ont bien été modifiées'
            );

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('pages/user/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }
}
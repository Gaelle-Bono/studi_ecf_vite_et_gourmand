<?php

namespace App\Controller;

use App\Form\UserProfileFormType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash(
                'warning',
                'Connectez-vous pour modifier vos informations personnelles'
            );

            return $this->redirectToRoute('app_login');
        }

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

        return $this->render('pages/user/profile.html.twig', [
            'profileForm' => $form,
        ]);
    }
}
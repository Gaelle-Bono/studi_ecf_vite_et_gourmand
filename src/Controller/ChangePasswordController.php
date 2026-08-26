<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UpdatePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ChangePasswordController extends AbstractController
{
    #[Route('/change-password', name: 'app_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response {
        
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash(
                'warning',
                'Connectez-vous pour modifier votre mot de passe'
            );

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UpdatePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentPassword = $form->get('currentPassword')->getData();

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(
                    new \Symfony\Component\Form\FormError(
                        'Votre mot de passe actuel est incorrect'
                    )
                );
            } else {
                $plainPassword = $form->get('plainPassword')->getData();

                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );

                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a été modifié avec succès'
                );

                return $this->redirectToRoute('app_user_profile');
            }
        }

        return $this->render('pages/user/change_password.html.twig', [
            'changePasswordForm' => $form,
        ]);

    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Constant\AppConstant;
use App\Form\RegistrationFormType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(MailService $mailService, RoleRepository $roleRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            //add 'ROLE_USER' to a new subscriber
            $role = $roleRepository->findOneBy(['name' => AppConstant::USER]);
            if (!$role) {
               throw new \LogicException('Le rôle ROLE_USER doit exister en base');
            } 
            
            $user->setRole($role);
        
            // register the user in DB with his role 
            $entityManager->persist($user);
            $entityManager->flush();

            $success = $mailService->sendMail($user->getEmail(), 'Bienvenue sur Vite et Gourmand!','emails/welcome/welcome.html.twig',
                ['user' => $user]
            );

            if (!$success) {
                $this->addFlash('warning', 'Compte créé mais l’email de confirmation n’a pas pu être envoyé');
            } else {
                $this->addFlash('success', 'Compte créé avec succès. Un email de bienvenue vous a été envoyé');
            }

            return $this->redirectToRoute('app_login');
        }
        return $this->render('pages/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}

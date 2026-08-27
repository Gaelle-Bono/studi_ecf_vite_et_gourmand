<?php

namespace App\Controller;

use App\Form\ContactFormType;

use App\Service\MailService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ContactController extends AbstractController
{

    public function __construct(private MailService $mailService)
    {
    }


    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {

        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $sent = $this->mailService->sendMail(
                'contact@vite-et-gourmand.fr',
                $data['title'],
                'emails/contact/contact_request.html.twig',
                [
                    'visitorEmail' => $data['email'],
                    'description' => $data['description'],
                ]
            );

            if ($sent) {
                $this->addFlash('success', 'Votre message a bien été envoyé');
            } else {
                $this->addFlash('danger', 'Une erreur est survenue lors de l’envoi de votre message');
            }

    
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/general/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Review;
use App\Entity\Order;
use App\Entity\User;

use App\Enum\OrderStatus;

use App\Form\ReviewFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class ReviewController extends AbstractController
{
    #[Route('/review/{id}', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Order $order, Request $request, EntityManagerInterface $em): Response 
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'Connectez-vous pour laisser un avis');
            
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas laisser un avis pour cette commande');
            
            return $this->redirectToRoute('app_order_my_orders');
        }

        if ($order->getOrderStatus() !== OrderStatus::COMPLETED) {
            $this->addFlash('danger', 'Vous ne pouvez laisser un avis que pour une commande terminée');
            
            return $this->redirectToRoute('app_order_my_orders');
        }

        $existingReview = $order->getReview();

        if ($existingReview) {
            $this->addFlash('warning', 'Vous avez déjà laissé un avis pour cette commande');
            
            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }


        $review = new Review();
        $review->setOrder($order);
        $review->setUser($user);
        $order->setReview($review);

        
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($review);
            $em->flush();

            $this->addFlash(
                'success',
                'Merci d’avoir pris le temps de partager votre expérience avec nous. Votre avis nous est précieux !'
            );

            return $this->redirectToRoute('app_order_show', [
                'id' => $order->getId(),
            ]);

        }

        return $this->render('pages/review/new.html.twig', [
            'form' => $form,
            'order' => $order,
        ]);

    }

}
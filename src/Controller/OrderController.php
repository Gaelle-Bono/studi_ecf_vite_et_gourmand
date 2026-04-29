<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Order;
use App\Form\OrderType;

use App\Repository\MenuRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/new', name: 'app_order_new')]
    #[Route('/new/{id}', name: 'app_order_new_with_menu')]
    public function new(?Menu $menu = null, Request $request, EntityManagerInterface $em, MenuRepository $menuRepository): Response 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // $order = new Order();
        // $order->setUser($this->getUser());

        // if ($menu) {
        //     $order->setMenu($menu);
        // }

        // $form = $this->createForm(OrderType::class, $order, [
        //     'menu_locked' => $menu !== null // 👈 important
        // ]);

        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {

        //     // 🔥 Vérification métier importante
        //     if ($order->getNumberOfPeople() < $order->getMenu()->getMinimumNumberOfPeople()) {
        //         $this->addFlash('danger', 'Nombre de personnes insuffisant.');
        //     } else {
        //         $order->setCreatedAt(new \DateTimeImmutable());

        //         $em->persist($order);
        //         $em->flush();

        //         // TODO : envoi mail

        //         $this->addFlash('success', 'Commande confirmée !');

        //         return $this->redirectToRoute('app_menu_index');
        //     }
        // }
        return $this->render('order/new.html.twig', [
            //'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }


}

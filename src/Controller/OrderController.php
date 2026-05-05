<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Menu;
use App\Form\OrderFormType;
use App\Repository\OrderStatusRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    #[Route('/new/{id}', name: 'app_order_new_with_menu', methods: ['GET', 'POST'])]
    public function new(
                ?Menu $menu = null,
                Request $request, 
                EntityManagerInterface $em,
                OrderStatusRepository $orderStatusRepository): Response 
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'Connectez-vous ou créez un compte pour passer votre commande.');
            return $this->redirectToRoute('app_login');
        }

        /** @var \App\Entity\User $user */
        
        $user = $this->getUser();
        

        $order = new Order();
        //fill Customer infos at order creation to keep a record even if user changes later
        $order
            ->setUser($user)
            ->setCustomerFirstNameAtOrder($user->getFirstName())
            ->setCustomerLastNameAtOrder($user->getLastName())
            ->setCustomerEmailAtOrder($user->getEmail())
            ->setCustomerPhoneAtOrder($user->getPhoneNumber());
        
            //fill menu details at order creation to keep a record even if menu changes later
        if ($menu) {
            $order
                ->setMenu($menu)
                ->setMenuTitleAtOrder($menu->getTitle())
                ->setMenuDescriptionAtOrder($menu->getDescription())
                ->setMenuPriceAtOrder($menu->getPricePerPerson());
        }

        $form = $this->createForm(OrderFormType::class, $order, [
             'menu_locked' => $menu !== null // if there is a menu, 'menu_locked' => true (acceded from Menu details page)
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $numberOfPeople = $order->getNumberOfPeople();

            if ($numberOfPeople < $order->getMenu()->getMinimumNumberOfPeople()) {
                $this->addFlash('danger', 'Nombre de personnes insuffisant.');
            } else {
                $serviceDate = $form->get('serviceDate')->getData();
                $requestedTime = $form->get('requestedTime')->getData();

                if ($serviceDate && $requestedTime) {
                    $order->setRequestedDeliveryAt(new \DateTimeImmutable(
                        $serviceDate->format('Y-m-d') . ' ' . $requestedTime->format('H:i'))
                    );
                } 

                $menuPrice = (float) $order->getMenuPriceAtOrder();
                $pricePerPerson = $menuPrice * $numberOfPeople;

                $servicePrice = 5.00; // base
                $deliveryPrice = 0.59 * 10; // exemple fixe (à adapter selon km)
                
                $total = $pricePerPerson + $servicePrice + $deliveryPrice;

                $order
                    ->setPricePerPersonAtOrder((string) $pricePerPerson)
                    ->setServicePriceAtOrder((string) $servicePrice)
                    ->setDeliveryPriceAtOrder((string) $deliveryPrice)
                    ->setTotalPriceAtOrder((string) $total)
                ;

                // initial status of the order is "En cours de préparation"
                $status = $orderStatusRepository->findOneBy(['code' => 'PENDING']);
                $order->setOrderStatus($status);


                $em->persist($order);
                $em->flush();

                // TODO : envoi mail

                $this->addFlash('success', 'Commande confirmée !');

                 return $this->redirectToRoute('app_menu_index');
            }
        }
        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }


}

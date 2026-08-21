<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Menu;
use App\Entity\User;

use App\Form\OrderFormType;

use App\Repository\MenuRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusHistoryRepository;


use App\Enum\OrderStatus;

use App\Service\StockMenuService;
use App\Service\OpeningHoursService;
use App\Service\OrderBuilderService;
use App\Service\OrderValidationService;
use App\Service\DeliveryRouteService;
use App\Service\OrderPricingService;
use App\Service\OrderFinalizationService;
use App\Service\MailService;
use App\Service\OrderUpdateService;
use App\Service\OrderCancellationService;

use App\Constant\AppConstant;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\FormError;



#[Route('/order')]
class OrderController extends AbstractController
{

    public function __construct(
        private StockMenuService $stockMenuService, 
        private OrderBuilderService $orderBuilderService,
        private OpeningHoursService $openingHoursService, 
        private OrderValidationService $orderValidationService, 
        private OrderFinalizationService $orderFinalizationService,
        private OrderPricingService $orderPricingService,
        private DeliveryRouteService $deliveryRouteService, 
        private MailService $mailService,
        private OrderUpdateService $orderUpdateService,
        private OrderCancellationService $orderCancellationService
        ) {}


    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    #[Route('/new/{id}', name: 'app_order_new_with_menu', methods: ['GET', 'POST'])]
    public function new(?Menu $menu, Request $request, EntityManagerInterface $em, SessionInterface $session): Response 
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'Connectez-vous ou créez un compte pour passer votre commande');
            
            return $this->redirectToRoute('app_login');
        }

        if ($menu) {
            $stockAlert = $this->stockMenuService->getStockAlert($menu);

            if ($stockAlert) {
                $this->addFlash($stockAlert['type'], $stockAlert['message']);

                return $this->redirectToRoute('app_menu_show', [
                    'id' => $menu->getId()
                ]);
            }
        }
      
        $order = new Order();

        /** @var User $user */
        $user = $this->getUser();
        $this->orderBuilderService->fillFromUser($order, $user);
    

        if ($menu) {
            $this->orderBuilderService->fillFromMenu($order,$menu);
        }

        $form = $this->createForm(OrderFormType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $order = $form->getData();
            $menu = $order->getMenu();
            
            //nb people
            $numberOfPeople = $order->getNumberOfPeople();

            //Requested Service Date
            $serviceDate = $form->get('serviceDate')->getData();
            $requestedTime = $form->get('requestedTime')->getData();

            //Validation
            $errors = $this->orderValidationService->validate(
                $menu, 
                $numberOfPeople, 
                $serviceDate,
                $requestedTime);

            foreach ($errors as $error) {
                $form
                    ->get($error['field'])
                    ->addError(new FormError($error['message']));
            }


            if ($form->isValid()) {

                $summary = $session->get('order_summary');

                $now = new \DateTimeImmutable();

                $this->orderFinalizationService->finalizeOrder(
                    $order, 
                    $menu, 
                    $serviceDate,
                    $requestedTime,
                    $summary, 
                    $now
                );

                $statusHistory = $this->orderFinalizationService->createInitialStatusHistory($order, $now);

                $em->persist($order);
                $em->persist($statusHistory);
                $em->flush();


                // sending a mail
                $success = $this->mailService->sendMail($order->getCustomerEmailAtOrder(), 'Confirmation de votre commande', 'emails/order_confirmation.html.twig',
                    ['order' => $order]
                );

                if (!$success) {
                    $this->addFlash(
                        'warning',
                        "Merci pour votre commande !\n
                        Votre commande n°" . $order->getOrderNumber() . " a bien été enregistrée, mais l’email de confirmation n’a pas pu être envoyé."
                    );
                } else {
                    $this->addFlash(
                        'success',
                        "Merci pour votre commande !\n
                        Votre commande n°" . $order->getOrderNumber() . " a bien été enregistrée.\n
                        Un email de confirmation vous a été envoyé."
                    );
                }

               

                return $this->redirectToRoute('app_order_show', [
                    'id' => $order->getId()
                ]);

            }
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'menu' => $menu,
            'groupDiscountPercent' => AppConstant::GROUP_DISCOUNT_PERCENT
        ]);
    }


    #[Route('/menu-preview', name: 'app_order_menu_preview', methods: ['POST'])]
    public function menuPreview(Request $request, MenuRepository $menuRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $menuId = $data['menuId'];

        $menu = $menuRepository->find($menuId);

        if (!$menu) {
            return $this->json([
                'success' => false,
                'message' => 'Le menu sélectionné n\'existe plus'
            ], 400);
        }


        return $this->json([
            'success' => true,
            'menu_html' => $this->renderView('menu/_menu_preview.html.twig', [
                'menu' => $menu
            ]),
        ]);
    }

    #[Route('/validate-menu-availability', name: 'app_order_validate_menu_availability', methods: ['POST'])]
    
    public function validate_menu_availability(Request $request, MenuRepository $menuRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $menu = $menuRepository->find($data['menuId']);

        if (!$menu) {
            return $this->json([
                'success' => false,
                'message' => 'Le menu sélectionné n\'existe plus'
            ], 400);
        }

        $stockAlert = $this->stockMenuService->getStockAlert($menu);

        
        if ($stockAlert) {
            return $this->json([
                'success' => false,
                'message' => $stockAlert['message']
            ]);
        }


        return $this->json([
            'success' => true
        ]);
    }


    #[Route('/validate-number-of-people', name: 'app_order_validate_number_of_people', methods: ['POST'])]
    public function validateNumberOfPeople( Request $request, OrderRepository $orderRepository): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);

        $orderId = $data['orderId'];

        $order = $orderRepository->find($orderId);

        if (!$order) {
            return $this->json([
                'success' => false,
                'message' => 'La commande sélectionnée n\'existe plus'
            ], 400);
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() !== $user) {
            return $this->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas modifier cette commande'
            ], 403);
        }

        $numberOfPeople = (int) $data['numberOfPeople'];
        $menu = $order->getMenu();

        $error = $this->orderValidationService->validateNumberOfPeople(
            $menu,
            $numberOfPeople,
            $order->getNumberOfPeople()
        );

        if ($error) {
            return $this->json([
                'success' => false,
                'message' => $error
            ]);
        }

        return $this->json([
            'success' => true
        ]);

    }


    #[Route('/summary', name: 'app_order_summary', methods: ['POST'])]
    public function summary(Request $request, MenuRepository $menuRepository,  SessionInterface $session): JsonResponse 
    {

        $data = json_decode($request->getContent(), true);
        
        $menuId = $data['menuId'];
        $menu = $menuRepository->find($menuId);
        
        if (!$menu) {
            return $this->json([
                'success' => false,
                'message' => 'Le menu sélectionné n\'existe plus'
            ], 400);
        }
                
        $serviceAddress = $data['address'];

        //calculate distance between company and service address
        try {
            $route = $this->deliveryRouteService->getRouteData($serviceAddress);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'blocking' => true
            ], 400);
        }
        
        $distance = $route['distance'];   
        
        if ($distance > AppConstant::MAX_DELIVERY_DISTANCE_KM) {
            return $this->json([
                'success' => false,
                'message' => 'Nous livrons uniquement dans un rayon de '
                . AppConstant::MAX_DELIVERY_DISTANCE_KM . ' km autour de notre établissement',
                'blocking' => true
            ],400);
        }
                
        $customer = $data['customer'];
    
        $serviceDate = new \DateTimeImmutable($data['serviceDate']);
        $requestedTime = $data['requestedTime'];
        
        $coordinates = $route['coordinates'];   
        $deliveryInstructions = $data['deliveryInstructions'] ?? null;
        
        $numberOfPeople = (int) $data['people'];
    

        //calculate prices
        $pricing = $this->orderPricingService->calculatePricesForOrder(
            $menu,
            $numberOfPeople,
            $distance
        );

        //stock datas in session for next step (order confirmation)
        $session->set('order_summary', [
            'customer' => $customer,
            'serviceAddress' => $serviceAddress,
            'deliveryInstructions' => $deliveryInstructions,
            'serviceDate' => $serviceDate->format('Y-m-d'),
            'requestedTime' => $requestedTime,
            'menuId' => $menuId,
            'numberOfPeople' => $numberOfPeople,
            'coordinates' => $coordinates,
            'distance' => $distance,
            'pricing' => $pricing
        ]);

        return $this->json([
            'success' => true,
            'summary_html' => $this->renderView('order/_summary.html.twig', [
                'customer' => $customer,    
                'serviceAddress' => $serviceAddress,
                'deliveryInstructions' => $deliveryInstructions,
                'serviceDate' => $serviceDate,
                'requestedTime' => $requestedTime,
                'menu' => $menu,
                'pricing' => $pricing,
                'groupDiscountPercent' => AppConstant::GROUP_DISCOUNT_PERCENT,
                'numberOfPeople' => $numberOfPeople
            ])
        ]);

    }


    #[Route('/available_times', name: 'app_order_available_times', methods: ['POST'])]
    public function availableTimes(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $serviceDate = $data['serviceDate'];

        try {
            $date = new \DateTimeImmutable($serviceDate);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Date invalide'
            ], 400);
        }

        $openingHoursData = $this->openingHoursService->getOpeningHoursForDate($date);

        if ($openingHoursData['isClosed']) {
            return $this->json([
                'success' => false,
                'isClosed' => true,
                'message' => $openingHoursData['message']
            ], 400);
        }

        return $this->json([
            'success' => true,
            'openingHoursText' => $openingHoursData['openingHoursText']
        ]);
    }

    #[Route('/{id}', name: 'app_order_show',  requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Order $order, OrderStatusHistoryRepository $orderStatusHistoryRepository): Response
    {

        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash(
                'warning',
                'Connectez-vous pour consulter votre commande'
            );

            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas accéder à cette commande');
            return $this->redirectToRoute('app_order_my_orders');
        }

        $canEdit = $order->getOrderStatus() === OrderStatus::PENDING;
        
        $statusHistory = $orderStatusHistoryRepository->findByOrder($order);
        

        return $this->render('order/show.html.twig', [
            'order' => $order,
            'canEdit' => $canEdit,
            'statusHistory' => $statusHistory
        ]);
    }

    #[Route('/my-orders', name: 'app_order_my_orders', methods: ['GET'])]
    public function myOrders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('order/my_orders.html.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Order $order, Request $request, EntityManagerInterface $em, SessionInterface $session): Response
    {
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash(
                'warning',
                'Connectez-vous pour modifier votre commande'
            );

            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($order->getUser() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier cette commande');
            return $this->redirectToRoute('app_order_my_orders');
        }

        if ($order->getOrderStatus() !== OrderStatus::PENDING) {
            $this->addFlash(
                'warning',
                'Cette commande ne peut plus être modifiée'
            );

            return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
        }

        $form = $this->createForm(OrderFormType::class, $order, [
            'edit' => true,
        ]);

        $form->get('serviceDate')->setData($order->getRequestedDeliveryAt());
        $form->get('requestedTime')->setData($order->getRequestedDeliveryAt()->format('H:i:s'));

        $oldNumberOfPeople = $order->getNumberOfPeople();

        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            $order = $form->getData();
            $menu = $order->getMenu();

            $numberOfPeople = $order->getNumberOfPeople();
            $currentNumberOfPeople = $oldNumberOfPeople;


            $serviceDate = $form->get('serviceDate')->getData();
            $requestedTime = $form->get('requestedTime')->getData();

     
            // Validation
            $errors = $this->orderValidationService->validate(
                $menu,
                $numberOfPeople,
                $serviceDate,
                $requestedTime,
                $currentNumberOfPeople
            );

            foreach ($errors as $error) {
                $form
                    ->get($error['field'])
                    ->addError(new FormError($error['message']));
            }

            if ($form->isValid()) {

                $summary = $session->get('order_summary');

                $this->orderUpdateService->updateOrder(
                    $order,
                    $summary,
                    $serviceDate,
                    $requestedTime
                );

                // Stock management
                $difference = $numberOfPeople - $currentNumberOfPeople;

                $menu->setRemainingQuantity(
                    $menu->getRemainingQuantity() - $difference
                );

                $em->persist($order);
                $em->flush();

                $session->remove('order_summary');

                $this->addFlash(
                    'success',
                    "Votre commande n°" . $order->getOrderNumber() . " a bien été modifiée."
                );

                return $this->redirectToRoute('app_order_show', [
                    'id' => $order->getId()
                ]);
            }
        }

        return $this->render('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
      
    }


    #[Route('/{id}/cancel', name: 'app_order_cancel', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function cancel(Order $order, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('cancel'.$order->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        } 

        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($order->getOrderStatus() !== OrderStatus::PENDING) {
            $this->addFlash(
                'warning',
                'Cette commande ne peut plus être annulée'
            );

            return $this->redirectToRoute('app_order_show', [
                'id' => $order->getId()
            ]);
        }

        $now = new \DateTimeImmutable();
        $this->orderCancellationService->cancel($order, $now);
        $statusHistory = $this->orderCancellationService->createStatusHistory($order, $now);
        $cancellation = $this->orderCancellationService->createCancellation($order);
        
        $em->persist($order);
        $em->persist($statusHistory);
        $em->persist($cancellation);

        $em->flush();

    
        $this->addFlash(
            'success',
            'La commande ' . $order->getOrderNumber() . ' a été annulée avec succès'
        );

        return $this->redirectToRoute('app_order_my_orders');

    }


}

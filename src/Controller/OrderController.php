<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Menu;
use App\Entity\User;

use App\Form\OrderFormType;

use App\Repository\MenuRepository;

use App\Service\StockMenuService;
use App\Service\OrderBuilderService;
use App\Service\OrderValidationService;
use App\Service\OrderFinalizationService;
use App\Service\OrderPricingService;
use App\Service\OrderDistanceService;

use App\Constant\AppConstant;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;



#[Route('/order')]
class OrderController extends AbstractController
{

    public function __construct(
        private StockMenuService $stockMenuService, 
        private OrderBuilderService $orderBuilderService, 
        private OrderValidationService $orderValidationService, 
        private OrderFinalizationService $orderFinalizationService,
        private OrderPricingService $orderPricingService,
        private OrderDistanceService $orderDistanceService,
        ) {
    }


    #[Route('/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    #[Route('/new/{id}', name: 'app_order_new_with_menu', methods: ['GET', 'POST'])]
    public function new(?Menu $menu, Request $request, EntityManagerInterface $em): Response 
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

        if ($user) {
            $this->orderBuilderService->fillFromUser($order, $user);
        }

        if ($menu) {
            $this->orderBuilderService->fillFromMenu($order,$menu);
        }

        $form = $this->createForm(OrderFormType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $menu = $order->getMenu();
            
            if (!$menu) {
                $this->addFlash('danger', 'Veuillez sélectionner un menu pour passer votre commande');

                return $this->redirectToRoute('app_order_new');
            }


            $serviceDate = $form->get('serviceDate')->getData();
            $requestedTime = $form->get('requestedTime')->getData();

            //Validation
            $error = $this->orderValidationService->validate($order, $menu, $serviceDate);

            if ($error) {
                $this->addFlash('danger', $error);

                return $this->redirectToRoute('app_order_new_with_menu', [
                    'id' => $menu->getId()
                ]);
            }

            $this->orderFinalizationService->finalizeOrder($order, $menu, $serviceDate, $requestedTime);

            $em->persist($order);
            $em->flush();

            // TODO : envoi mail
            
            $this->addFlash('success', 'Commande confirmée !');

            return $this->redirectToRoute('app_menu_index');
        }

        return $this->render('order/new.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'menu' => $menu,
            'groupDiscountPercent' => AppConstant::GROUP_DISCOUNT_PERCENT
        ]);
    }


    #[Route('/menu-preview', name: 'app_order_menu_preview', methods: ['POST'])]
    public function menuPreview(Request $request,MenuRepository $menuRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $menuId = $data['menuId'] ?? null;

        if (!$menuId) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez sélectionner un menu',
                'menu_html' => '<p class="text-warning">Veuillez sélectionner un menu</p>'
            ],400);
        }

        $menu = $menuRepository->find($menuId);

        if (!$menu) {
            return $this->json([
                'success' => false,
                'message' => 'Menu introuvable',
                'menu_html' => '<p class="text-danger">Menu introuvable</p>'
            ], 404);
        }


        return $this->json([
            'success' => true,
            'menu_html' => $this->renderView('menu/_menu_preview.html.twig', [
                'menu' => $menu
            ])
        ]);

    }


    #[Route('/summary', name: 'app_order_summary', methods: ['POST'])]
    public function summary(Request $request, MenuRepository $menuRepository): JsonResponse 
    {

        $data = json_decode($request->getContent(), true);

        $menuId = $data['menuId'] ?? null;
        $numberOfPeople = (int) ($data['people'] ?? 0);
        $serviceAddress = $data['address'] ?? null;

        if (!$menuId 
            || !$numberOfPeople 
            || empty($serviceAddress['street'])
            || empty($serviceAddress['zip'])
            || empty($serviceAddress['city'])) {
            return $this->json([
                'success' => false,
                'message' => 'Informations incomplètes.',
                'summary_html' => '<p class="text-danger">Informations incomplètes.</p>',
            ], 400);
        }

        $menu = $menuRepository->find($menuId);

        if (!$menu) {
            return $this->json([
                'success' => false,
                'message' => 'Menu introuvable.',
                'summary_html' => '<p class="text-danger">Menu introuvable</p>'
            ], 404);
        }

        //calculate distance between company and service address
        try {
            $distance = $this->orderDistanceService->calculateDistanceFromAddresses($serviceAddress);
        } catch (\RuntimeException $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage(),
                'summary_html' => '<p class="text-danger">' . $e->getMessage() . '</p>'
            ], 400);
        }
            
        //calculate prices
        $pricing = $this->orderPricingService->calculatePricesForOrder(
            $menu,
            $numberOfPeople,
            $distance
        );

        return $this->json([
            'success' => true,
            'summary_html' => $this->renderView('order/_summary.html.twig', [
                'menu' => $menu,
                'pricing' => $pricing,
                'groupDiscountPercent' => AppConstant::GROUP_DISCOUNT_PERCENT,
                'numberOfPeople' => $numberOfPeople
            ])
        ]);

    }
}

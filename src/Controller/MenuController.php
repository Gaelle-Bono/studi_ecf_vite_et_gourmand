<?php

namespace App\Controller;

use App\Repository\MenuRepository; 
use App\Repository\ThemeRepository; 
use App\Repository\DietRepository;
use App\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\Routing\Attribute\Route; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response; 

use App\Service\FilterMenuService;
use App\Service\StockMenuService;

#[Route('/menu')] 
class MenuController extends AbstractController 
{
    #[Route('', name: 'app_menu_index', methods: ['GET'])] 
    public function index(MenuRepository $menuRepository, 
                    DietRepository $dietRepository, 
                    ThemeRepository $themeRepository): Response 
    { 
        return $this->render('pages/menu/index.html.twig', [
            // Get all menus, themes, and diets 
            'menus' => $menuRepository->findAll(),
            'diets' => $dietRepository->findAll(), 
            'themes' => $themeRepository->findAll(), 
        ]); 
    } 

    #[Route('/filter', name: 'app_menu_filter', methods: ['POST'])] 
    public function filter(Request $request, 
                        MenuRepository $menuRepository, 
                        DietRepository $dietRepository, 
                        ThemeRepository $themeRepository, 
                        FilterMenuService $filterMenuService): JsonResponse 
    { 
        $rawFilters = [
            'diet' => $request->request->get('diet'),
            'theme' => $request->request->get('theme'),
            'minPricePerPerson' => $request->request->get('minPricePerPerson'),
            'maxPricePerPerson' => $request->request->get('maxPricePerPerson'),
            'minimumNumberOfPeople' => $request->request->get('minimumNumberOfPeople'),
        ];

        $result = $filterMenuService->validateFilters($rawFilters,$dietRepository,$themeRepository);

        $filters = $result['filters'];
        $errors = $result['errors'];

        
        //if errors
        if (!empty($errors)) {
            return $this->json([
                'success' => false,
                'menus_list' => '',
                'errors' => $errors
            ], 400);
        }

        $menus = $menuRepository->findWithFilters(
            $filters['diet'],
            $filters['theme'],
            $filters['minPricePerPerson'],
            $filters['maxPricePerPerson'],
            $filters['minimumNumberOfPeople']
        );

        if (empty($menus)) {
            return $this->json([
                'success' => true,
                'menus_list' => '<p class="text-warning">
                Aucun menu ne correspond à vos critères de recherche</p>',
                'errors' => []
            ]);
        }

        return $this->json([
            'success' => true,
            'menus_list' => $this->renderView('pages/menu/_list.html.twig', [
                'menus' => $menus
            ]),
            'errors' => []
        ]);
            
    } 

    #[Route('/{id}', name: 'app_menu_show', requirements: ['id' => '\d+'], methods: ['GET'])] 
    public function show(
        Menu $menu, StockMenuService $stockMenuService): Response 
    {

        return $this->render('pages/menu/show.html.twig', [
            'menu' => $menu,
            // stock
            'stockAlert' => $stockMenuService->getStockAlert($menu),
        ]);
    }

}


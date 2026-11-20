<?php

namespace App\Controller;

use App\Entity\Menu;

use App\Repository\MenuRepository;


use App\Service\StockMenuService;



use App\Form\MenuFormType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



    #[Route('/management/menu')]

    class EmployeeMenuController extends AbstractController
    {

    #[Route('', name: 'app_employee_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('pages/employee/menu/index.html.twig', [
            'menus' => $menuRepository->findAllMenus()
        ]);
    }

    #[Route('/{id}', name: 'app_employee_menu_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Menu $menu, StockMenuService $stockMenuService): Response
    {
        return $this->render('pages/employee/menu/show.html.twig', [
            'menu' => $menu,
            'stockAlert' => $stockMenuService->getStockAlert($menu),
        ]);
    }


    #[Route('/{id}/toggle-active', name: 'app_employee_menu_toggle_active', methods: ['POST'])]
    public function toggleActive(Menu $menu, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('toggle-menu-' . $menu->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $menu->setIsActive(!$menu->isActive());

        $em->flush();

        if ($menu->isActive()) {
            $this->addFlash('success', 'Le menu "' . $menu->getTitle() . '" a bien été activé');
        } else {
            $this->addFlash('success', 'Le menu "' . $menu->getTitle() . '" a bien été désactivé');
        }

        return $this->redirectToRoute('app_employee_menu_index');
    }

    

    #[Route('/{id}/edit', name: 'app_employee_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Menu $menu, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuFormType::class, $menu);

        // Pre-fill the conditions field with the existing conditions as a string
        $conditions = '';

        if ($menu->getConditions()) {
            $conditions = implode("\n", $menu->getConditions());
        }

        $form->get('conditionsText')->setData($conditions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Convert the entered conditions into an array
            $conditionsText = $form->get('conditionsText')->getData();
            $conditions = [];

            foreach (explode("\n", $conditionsText) as $condition) {
                $condition = trim($condition);

                if ($condition !== '') {
                    $conditions[] = $condition;
                }
            }

            if (empty($conditions)) {
                $menu->setConditions(null);
            } else {
                $menu->setConditions($conditions);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le menu "' . $menu->getTitle() . '" a bien été modifié');

            return $this->redirectToRoute('app_employee_menu_show', [
                'id' => $menu->getId()
            ]);
        }

        return $this->render('pages/employee/menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form
        ]);
    }


}
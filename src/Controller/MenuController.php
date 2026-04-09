<?php

namespace App\Controller;

use App\Repository\MenuRepository; 
use App\Repository\ThemeRepository; 
use App\Repository\DietRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\Routing\Attribute\Route; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response; 

#[Route('/menu')] 
class MenuController extends AbstractController 
{
    #[Route('', name: 'app_menu_index', methods: ['GET'])] 
    public function index(MenuRepository $menuRepository, ThemeRepository $themeRepository, DietRepository $dietRepository): Response 
    { 
        return $this->render('menu/index.html.twig', [
            // Get all menus, themes, and diets 
            'menus' => $menuRepository->findAll(),
            'themes' => $themeRepository->findAll(), 
            'diets' => $dietRepository->findAll(), 
        ]); 
    } 
    

    #[Route('/filter', name: 'app_menu_filter', methods: ['POST'])] 
    public function filter(Request $request, MenuRepository $menuRepository): JsonResponse 
    { 
        // Get filters from request
        $filters = [ 
            'diet' => $request->request->get('diet'), 
            'theme' => $request->request->get('theme'), 
            'minPricePerPerson' => $request->request->get('minPricePerPerson'), 
            'maxPricePerPerson' => $request->request->get('maxPricePerPerson'), 
            'minimumNumberOfPeople' => $request->request->get('minimumNumberOfPeople'), 
        ]; 

        $errors = [];
        // Validate and cast fields
        $this->validateAndCastFields($filters, $errors);

        if (empty($errors)) { 
            // Fetch menus using filters if valid
            $menus = $menuRepository->findWithFilters( 
                $filters['diet'] ?? null, 
                $filters['theme'] ?? null, 
                $filters['minPricePerPerson'] ?? null, 
                $filters['maxPricePerPerson'] ?? null, 
                $filters['minimumNumberOfPeople'] ?? null
            ); 
        } else { 
            $menus = []; 
        } 
        
        return $this->json([ 
            'menus_list' => $this->renderView('menu/_list.html.twig', ['menus' => $menus]), 
            'errors' => $errors 
        ]); 
    } 


    private function validateAndCastFields(array &$filters, array &$errors): void
    {
        // Assign filters to local variables for easier reading
        $diet = $filters['diet'] ?? null;
        $theme = $filters['theme'] ?? null;
        $minPrice = $filters['minPricePerPerson'] ?? null;
        $maxPrice = $filters['maxPricePerPerson'] ?? null;
        $minPeople = $filters['minimumNumberOfPeople'] ?? null;

        // --- Validate select fields ---
        $diet = $this->validateSelectField($diet, 'diet', $errors);  
        $theme = $this->validateSelectField($theme, 'theme', $errors);  

        // --- Validate price fields ---
        $minPrice = $this->validatePriceField($minPrice, 'minPricePerPerson', $errors);  
        $maxPrice = $this->validatePriceField($maxPrice, 'maxPricePerPerson', $errors);  

        // --- Validate minimum number of people ---
        $minPeople = $this->validateMinPeopleField($minPeople, 'minimumNumberOfPeople', $errors);  

        // --- Logical validation ---
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            $errors['maxPricePerPerson'] = 'Le prix maximum doit être supérieur ou égal au prix minimum.';
        }

        // Save validated values back into filters array
        $filters['diet'] = $diet;
        $filters['theme'] = $theme;
        $filters['minPricePerPerson'] = $minPrice;
        $filters['maxPricePerPerson'] = $maxPrice;
        $filters['minimumNumberOfPeople'] = $minPeople;
    }


    // Validate select fields (diet, theme)
    private function validateSelectField($value, string $fieldName, array &$errors): ?int
    {
        if ($value === null || $value === '') return null;

        if (!is_numeric($value)) {
            $errors[$fieldName] = 'Valeur invalide.';
            return null;
        }

        return (int)$value;
    }


    // Validate price fields (minPrice, maxPrice)
    private function validatePriceField($value, string $fieldName, array &$errors): ?float
    {
        if ($value === null || $value === '') return null;

        if (!is_numeric($value)) {
            $errors[$fieldName] = 'Valeur invalide.';
            return null;
        }

        $value = (float)$value;

        if ($value < 0) {
            $errors[$fieldName] = 'Le prix doit être positif.';
        }

        return $value;
    }


    // Validate minimum number of people
    private function validateMinPeopleField($value, string $fieldName, array &$errors): ?int
    {
        if ($value === null || $value === '') return null;

        if (!is_numeric($value) || (int)$value <= 0) {
            $errors[$fieldName] = 'Le nombre de personnes doit être positif.';
            return null;
        }

        return (int)$value;
    }
}
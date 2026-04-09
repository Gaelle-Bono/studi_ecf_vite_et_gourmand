<?php

namespace App\Controller;

use App\Repository\MenuRepository; 
use App\Repository\ThemeRepository; 
use App\Repository\DietRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\Routing\Attribute\Route; 
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response; 

#[Route('/menu')] 
class MenuController extends AbstractController 
{
    #[Route('', name: 'app_menu_index', methods: ['GET'])] 
    public function index(MenuRepository $menuRepository, DietRepository $dietRepository, ThemeRepository $themeRepository): Response 
    { 
        return $this->render('menu/index.html.twig', [
            // Get all menus, themes, and diets 
            'menus' => $menuRepository->findAll(),
            'diets' => $dietRepository->findAll(), 
            'themes' => $themeRepository->findAll(), 
        ]); 
    } 
    

    #[Route('/filter', name: 'app_menu_filter', methods: ['POST'])] 
    public function filter(Request $request, MenuRepository $menuRepository, DietRepository $dietRepository, ThemeRepository $themeRepository): JsonResponse 
    { 
        // Get filters from request : raw data 
        $filters = [ 
            'diet' => $request->request->get('diet'), 
            'theme' => $request->request->get('theme'), 
            'minPricePerPerson' => $request->request->get('minPricePerPerson'), 
            'maxPricePerPerson' => $request->request->get('maxPricePerPerson'), 
            'minimumNumberOfPeople' => $request->request->get('minimumNumberOfPeople'), 
        ]; 

        $menus= [];
        $errors = [];
        // Validate and cast fields
        $this->validateAndCastFields($filters, $errors, $dietRepository, $themeRepository);

        if (empty($errors)) { 
            // Fetch menus using filters if valid
            $menus = $menuRepository->findWithFilters( 
                $filters['diet'] ?? null, 
                $filters['theme'] ?? null, 
                $filters['minPricePerPerson'] ?? null, 
                $filters['maxPricePerPerson'] ?? null, 
                $filters['minimumNumberOfPeople'] ?? null
            ); 
        } 
        
        return $this->json([ 
            'menus_list' => $this->renderView('menu/_list.html.twig', ['menus' => $menus]), 
            'errors' => $errors 
        ]); 
    } 


    private function validateAndCastFields(array &$filters, array &$errors, DietRepository $dietRepository, ThemeRepository $themeRepository): void
    {
        // Assign filters to local variables for easier reading
        $diet = $filters['diet'] ?? null;
        $theme = $filters['theme'] ?? null;
        $minPrice = $filters['minPricePerPerson'] ?? null;
        $maxPrice = $filters['maxPricePerPerson'] ?? null;
        $minPeople = $filters['minimumNumberOfPeople'] ?? null;

        // --- Validate select fields ---
        $diet = $this->validateSelectField($diet, 'diet', $errors, $dietRepository);  
        $theme = $this->validateSelectField($theme, 'theme', $errors, $themeRepository);  

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


    private function parseNumber($value, string $fieldName, array &$errors, string $type = 'int'):int|float|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($type === 'int') {
            if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                $errors[$fieldName] = 'La valeur doit être un nombre entier.';
                return null;
            }
            return (int)$value;
        }

        if ($type === 'float') {
            if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
                $errors[$fieldName] = 'La valeur doit être un nombre.';
                return null;
            }
            return (float)$value;
        }
        
        // in case of unexpected type, return error
        $errors[$fieldName] = 'Valeur invalide.';
        return null;
    }

    private function validateSelectField($value, string $fieldName, array &$errors, EntityRepository $repository): ?int
    {
        $id = $this->parseNumber($value, $fieldName, $errors, 'int');

        if ($id === null) {
            return null;
        }

        // Check if the ID exists in the database
        if (!$repository->find($id)) {
            $errors[$fieldName] = 'Valeur inexistante.';
            return null;
        }

        return $id;
    }

    
    private function validatePriceField($value, string $fieldName, array &$errors): ?float
    {
        $value = $this->parseNumber($value, $fieldName, $errors, 'float');

        if ($value !== null && $value < 0) {
            $errors[$fieldName] = 'Le prix doit être positif.';
            return null;
        }

        return $value;
    }

    private function validateMinPeopleField($value, string $fieldName, array &$errors): ?int
    {
        $value = $this->parseNumber($value, $fieldName, $errors, 'int');

        if ($value !== null && $value <= 0) {
            $errors[$fieldName] = 'Le nombre minimum de personnes doit être supérieur à zéro.';
            return null;
        }

        return $value;
    }
}
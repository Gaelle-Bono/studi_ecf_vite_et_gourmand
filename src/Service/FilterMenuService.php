<?php

namespace App\Service;

use App\Repository\DietRepository;
use App\Repository\ThemeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class FilterMenuService
{
    public function validateFilters(array $filters, DietRepository $dietRepository, ThemeRepository $themeRepository): array 
    {
        $errors = [];

        $diet = $filters['diet'] ?? null;
        $theme = $filters['theme'] ?? null;
        $minPrice = $filters['minPricePerPerson'] ?? null;
        $maxPrice = $filters['maxPricePerPerson'] ?? null;
        $minPeople = $filters['minimumNumberOfPeople'] ?? null;

        // validate select fields
        $diet = $this->validateSelectField($diet, 'diet', $errors, $dietRepository);
        $theme = $this->validateSelectField($theme, 'theme', $errors, $themeRepository);

        // validate numbers
        $minPrice = $this->validatePriceField($minPrice, 'minPricePerPerson', $errors);
        $maxPrice = $this->validatePriceField($maxPrice, 'maxPricePerPerson', $errors);
        $minPeople = $this->validateMinPeopleField($minPeople, 'minimumNumberOfPeople', $errors);

        // logical validation
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            $errors['maxPricePerPerson'] = 'Le prix maximum doit être supérieur ou égal au prix minimum';
        }

        return [
            'filters' => [
                'diet' => $diet,
                'theme' => $theme,
                'minPricePerPerson' => $minPrice,
                'maxPricePerPerson' => $maxPrice,
                'minimumNumberOfPeople' => $minPeople,
            ],
            'errors' => $errors
        ];
    }

    private function parseNumber(mixed $value, string $fieldName, array &$errors, string $type = 'int'): int|float|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($type === 'int') {
            if (!filter_var($value, FILTER_VALIDATE_INT)) {
                $errors[$fieldName] = 'La valeur doit être un nombre entier';
                return null;
            }
            return (int)$value;
        }

        if ($type === 'float') {
            if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                $errors[$fieldName] = 'La valeur doit être un nombre';
                return null;
            }
            return (float)$value;
        }

        $errors[$fieldName] = 'Valeur invalide';
        return null;
    }

    private function validateSelectField(mixed $value, string $fieldName, array &$errors, ServiceEntityRepository $repository): ?int
    {
        $id = $this->parseNumber($value, $fieldName, $errors, 'int');

        if ($id === null) {
            return null;
        }

        if (!$repository->find($id)) {
            $errors[$fieldName] = 'Valeur inexistante';
            return null;
        }

        return $id;
    }

    private function validatePriceField(mixed $value, string $fieldName, array &$errors): ?float
    {
        $value = $this->parseNumber($value, $fieldName, $errors, 'float');

        if ($value !== null && $value < 0) {
            $errors[$fieldName] = 'Le prix doit être positif';
            return null;
        }

        return $value;
    }

    private function validateMinPeopleField(mixed $value, string $fieldName, array &$errors): ?int
    {
        $value = $this->parseNumber($value, $fieldName, $errors, 'int');

        if ($value !== null && $value <= 0) {
            $errors[$fieldName] = 'Le nombre minimum de personnes doit être supérieur à zéro';
            return null;
        }

        return $value;
    }
}
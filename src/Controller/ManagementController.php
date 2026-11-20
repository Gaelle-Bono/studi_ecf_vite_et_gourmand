<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManagementController extends AbstractController
{
    #[Route('/management', name: 'app_management_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/management/index.html.twig');
    }
}
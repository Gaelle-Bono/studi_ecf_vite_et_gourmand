<?php

namespace App\Controller;

use App\Repository\ReviewRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(ReviewRepository $reviewRepository)
    {
        $reviews = $reviewRepository->findApprovedReviews();

        return $this->render('pages/home/home.html.twig', [
            'reviews' => $reviews
        ]);
    }
}
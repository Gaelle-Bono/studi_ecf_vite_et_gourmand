<?php

namespace App\Controller;

use App\Repository\ReviewRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/', name: 'app_home')]
    public function index(ReviewRepository $reviewRepository)
    {
        $reviews = $reviewRepository->findApprovedReviews();

        return $this->render('default/home.html.twig', [
            'reviews' => $reviews
        ]);
    }
}
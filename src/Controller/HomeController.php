<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(UniversityRepository $universityRepository): Response
    {
        return $this->render('university/home.html.twig', [
            'universities' => $universityRepository->getAllWithQueryBuilder(),
        ]);
    }
}
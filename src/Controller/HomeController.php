<?php

namespace App\Controller;

use App\Repository\CountryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CountryRepository $countryRepository): Response
    {
        $countriesCount = $countryRepository->count([]);
        return $this->render('home/index.html.twig', [
            'countriesCount' => $countriesCount,
        ]);
    }
}
